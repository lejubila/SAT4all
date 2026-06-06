<?php

namespace App\Tools\RegexTester;

class RegexTester
{
    private const MAX_SUBJECT_LENGTH = 10_000;
    private const MAX_MATCHES = 500;

    public function test(string $pattern, string $flags, string $subject): array
    {
        if ($pattern === '') {
            return ['idle' => true];
        }

        $phpPattern = $this->buildPattern($pattern, $flags);

        if (! $this->isValidPattern($phpPattern)) {
            return [
                'idle'        => false,
                'valid'       => false,
                'error'       => $this->getPatternError($phpPattern),
                'match_count' => 0,
                'matches'     => [],
                'highlighted' => htmlspecialchars($subject),
                'truncated'   => false,
            ];
        }

        $count = @preg_match_all($phpPattern, $subject, $raw, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        if ($count === false) {
            return [
                'idle'        => false,
                'valid'       => false,
                'error'       => 'Regex execution failed.',
                'match_count' => 0,
                'matches'     => [],
                'highlighted' => htmlspecialchars($subject),
                'truncated'   => false,
            ];
        }

        $truncated = $count > self::MAX_MATCHES;
        $raw       = array_slice($raw, 0, self::MAX_MATCHES);
        $matches   = $this->formatMatches($raw);

        return [
            'idle'        => false,
            'valid'       => true,
            'error'       => null,
            'match_count' => $count,
            'matches'     => $matches,
            'highlighted' => $this->buildHighlighted($subject, $matches),
            'truncated'   => $truncated,
        ];
    }

    public function replace(string $pattern, string $flags, string $subject, string $replacement): string|false
    {
        $phpPattern = $this->buildPattern($pattern, $flags);

        if (! $this->isValidPattern($phpPattern)) {
            return false;
        }

        return @preg_replace($phpPattern, $replacement, $subject) ?? false;
    }

    private function buildPattern(string $pattern, string $flags): string
    {
        $safeFlags = preg_replace('/[^imsuxX]/', '', $flags);
        return '~' . str_replace('~', '\~', $pattern) . '~' . $safeFlags;
    }

    private function isValidPattern(string $pattern): bool
    {
        set_error_handler(fn () => true);
        $ok = @preg_match($pattern, '') !== false;
        restore_error_handler();
        return $ok;
    }

    private function getPatternError(string $pattern): string
    {
        $error = '';
        set_error_handler(function ($errno, $errstr) use (&$error) {
            $error = $errstr;
            return true;
        });
        @preg_match($pattern, '');
        restore_error_handler();
        return preg_replace('/^preg_match\(\):\s*/', '', $error) ?: 'Invalid pattern';
    }

    private function formatMatches(array $raw): array
    {
        $result = [];
        foreach ($raw as $set) {
            $full   = $set[0];
            $groups = [];
            for ($i = 1; $i < count($set); $i++) {
                $groups[] = [
                    'index' => $i,
                    'value' => $set[$i][0],
                    'start' => $set[$i][1],
                    'end'   => $set[$i][1] + strlen($set[$i][0]),
                    'found' => $set[$i][1] !== -1,
                ];
            }
            $result[] = [
                'value'  => $full[0],
                'start'  => $full[1],
                'end'    => $full[1] + strlen($full[0]),
                'groups' => $groups,
            ];
        }
        return $result;
    }

    private function buildHighlighted(string $subject, array $matches): string
    {
        if (empty($matches)) {
            return nl2br(htmlspecialchars($subject));
        }

        $result = '';
        $pos    = 0;
        foreach ($matches as $m) {
            if ($m['start'] > $pos) {
                $result .= nl2br(htmlspecialchars(substr($subject, $pos, $m['start'] - $pos)));
            }
            $result .= '<mark class="rounded bg-amber-200 px-0.5 text-amber-900">'
                . htmlspecialchars($m['value'])
                . '</mark>';
            $pos = $m['end'];
        }
        if ($pos < strlen($subject)) {
            $result .= nl2br(htmlspecialchars(substr($subject, $pos)));
        }
        return $result;
    }
}
