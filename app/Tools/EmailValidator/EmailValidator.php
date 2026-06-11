<?php

namespace App\Tools\EmailValidator;

class EmailValidator
{
    private const SMTP_TIMEOUT = 5;

    public function __construct(private readonly string $input) {}

    public function validate(): array
    {
        $email = trim($this->input);

        $syntax = $this->checkSyntax($email);
        if (! $syntax['valid']) {
            return [
                'email'   => $email,
                'syntax'  => $syntax,
                'mx'      => ['found' => false, 'records' => []],
                'smtp'    => ['checked' => false, 'result' => 'skipped', 'code' => null, 'message' => null],
                'overall' => 'invalid',
            ];
        }

        $domain = $syntax['domain'];

        $mx = $this->checkMx($domain);
        if (! $mx['found']) {
            return [
                'email'   => $email,
                'syntax'  => $syntax,
                'mx'      => $mx,
                'smtp'    => ['checked' => false, 'result' => 'skipped', 'code' => null, 'message' => null],
                'overall' => 'invalid',
            ];
        }

        $smtp    = $this->checkSmtp($email, $domain, $mx['records'][0]['host'] ?? '');
        $overall = $this->resolveOverall($smtp);

        return [
            'email'   => $email,
            'syntax'  => $syntax,
            'mx'      => $mx,
            'smtp'    => $smtp,
            'overall' => $overall,
        ];
    }

    private function checkSyntax(string $email): array
    {
        $valid = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (! $valid) {
            return ['valid' => false, 'local' => '', 'domain' => ''];
        }

        [$local, $domain] = explode('@', $email, 2);

        return ['valid' => true, 'local' => $local, 'domain' => $domain];
    }

    private function checkMx(string $domain): array
    {
        $records = @dns_get_record($domain, DNS_MX);

        if (empty($records)) {
            // Fallback: domain with A record can still receive mail
            $a = @dns_get_record($domain, DNS_A);
            if (! empty($a)) {
                return [
                    'found'   => true,
                    'records' => [['host' => $domain, 'pri' => 0]],
                    'fallback' => true,
                ];
            }
            return ['found' => false, 'records' => [], 'fallback' => false];
        }

        usort($records, fn ($a, $b) => $a['pri'] <=> $b['pri']);

        return [
            'found'    => true,
            'records'  => array_map(fn ($r) => [
                'host' => rtrim($r['target'], '.'),
                'pri'  => $r['pri'],
            ], $records),
            'fallback' => false,
        ];
    }

    private function checkSmtp(string $email, string $domain, string $mxHost): array
    {
        if (empty($mxHost)) {
            return ['checked' => false, 'result' => 'unavailable', 'code' => null, 'message' => null];
        }

        $socket = @stream_socket_client(
            "tcp://{$mxHost}:25",
            $errno,
            $errstr,
            self::SMTP_TIMEOUT
        );

        if (! $socket) {
            return ['checked' => false, 'result' => 'unavailable', 'code' => null, 'message' => null];
        }

        stream_set_timeout($socket, self::SMTP_TIMEOUT);

        // Read banner
        $banner = $this->readLine($socket);
        if (! $banner || ! str_starts_with($banner, '220')) {
            fclose($socket);
            return ['checked' => false, 'result' => 'unavailable', 'code' => null, 'message' => null];
        }

        // EHLO
        fwrite($socket, "EHLO email-validator.local\r\n");
        $this->readMultiLine($socket);

        // MAIL FROM
        fwrite($socket, "MAIL FROM:<>\r\n");
        $fromResp = $this->readLine($socket);
        if (! $fromResp || ! str_starts_with($fromResp, '250')) {
            $this->quit($socket);
            return ['checked' => false, 'result' => 'unavailable', 'code' => null, 'message' => null];
        }

        // RCPT TO for the real address
        fwrite($socket, "RCPT TO:<{$email}>\r\n");
        $rcptResp = $this->readLine($socket);
        $code     = (int) substr((string) $rcptResp, 0, 3);

        $catchAll = false;
        if ($code === 250) {
            // Test catch-all with a random nonexistent address
            $random   = bin2hex(random_bytes(8)) . '@' . $domain;
            fwrite($socket, "RSET\r\n");
            $this->readLine($socket);
            fwrite($socket, "MAIL FROM:<>\r\n");
            $this->readLine($socket);
            fwrite($socket, "RCPT TO:<{$random}>\r\n");
            $testResp = $this->readLine($socket);
            if (str_starts_with((string) $testResp, '250')) {
                $catchAll = true;
            }
        }

        $this->quit($socket);

        $result = match (true) {
            $catchAll       => 'catchall',
            $code === 250   => 'valid',
            $code >= 500    => 'invalid',
            $code >= 400    => 'risky',
            default         => 'unavailable',
        };

        return [
            'checked' => true,
            'result'  => $result,
            'code'    => $code,
            'message' => trim((string) substr((string) $rcptResp, 4)),
        ];
    }

    private function readLine($socket): ?string
    {
        $line = fgets($socket, 512);
        return $line !== false ? trim($line) : null;
    }

    private function readMultiLine($socket): void
    {
        while (true) {
            $line = fgets($socket, 512);
            if ($line === false || strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }
    }

    private function quit($socket): void
    {
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
    }

    private function resolveOverall(array $smtp): string
    {
        return match ($smtp['result']) {
            'valid'       => 'valid',
            'invalid'     => 'invalid',
            'catchall'    => 'unknown',
            'risky'       => 'risky',
            default       => 'unknown',
        };
    }
}
