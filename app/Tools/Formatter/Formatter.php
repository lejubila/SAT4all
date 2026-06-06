<?php

namespace App\Tools\Formatter;

use DOMDocument;

class Formatter
{
    private const MAX_INPUT = 200_000;

    public function format(string $input, string $format, int $indent = 4): array
    {
        if ($input === '') {
            return ['idle' => true];
        }

        if (strlen($input) > self::MAX_INPUT) {
            return ['idle' => false, 'valid' => false, 'error' => 'too_large'];
        }

        $resolvedFormat = $format === 'auto' ? $this->detect($input) : $format;

        $result = match ($resolvedFormat) {
            'json'  => $this->formatJson($input, $indent),
            'xml'   => $this->formatXml($input, $indent),
            'html'  => $this->formatHtml($input, $indent),
            default => ['valid' => false, 'error' => 'unknown_format'],
        };

        // Always include the resolved format so the partial can display it
        $result['format'] = $resolvedFormat;

        if ($result['valid'] ?? false) {
            $result['size_in']  = strlen($input);
            $result['size_out'] = strlen($result['output']);
            $result['lines']    = substr_count($result['output'], "\n") + 1;
            $result['idle']     = false;
        }

        return $result;
    }

    private function detect(string $input): string
    {
        $trimmed = ltrim($input);

        // JSON: starts with { or [
        if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
            json_decode($input);
            if (json_last_error() === JSON_ERROR_NONE) {
                return 'json';
            }
        }

        // XML: starts with < but not <!DOCTYPE or <html
        if (str_starts_with($trimmed, '<?xml') || (str_starts_with($trimmed, '<') && ! preg_match('/^<!DOCTYPE\s+html/i', $trimmed) && ! preg_match('/^<html/i', $trimmed))) {
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            if (@$dom->loadXML($input)) {
                libxml_clear_errors();
                return 'xml';
            }
            libxml_clear_errors();
        }

        // HTML
        if (str_starts_with($trimmed, '<')) {
            return 'html';
        }

        return 'unknown';
    }

    private function formatJson(string $input, int $indent): array
    {
        $data = json_decode($input);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'valid' => false,
                'error' => 'invalid',
                'error_detail' => json_last_error_msg(),
            ];
        }

        $output = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($output === false) {
            return ['valid' => false, 'error' => 'invalid'];
        }

        if ($indent !== 4) {
            $output = $this->adjustIndent($output, 4, $indent);
        }

        return ['valid' => true, 'output' => $output];
    }

    private function formatXml(string $input, int $indent): array
    {
        libxml_use_internal_errors(true);

        $dom                    = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput      = true;

        $loaded = @$dom->loadXML($input);

        if (! $loaded) {
            $error = libxml_get_errors()[0] ?? null;
            libxml_clear_errors();
            return [
                'valid'        => false,
                'error'        => 'invalid',
                'error_detail' => $error ? trim($error->message) : 'Invalid XML',
            ];
        }

        libxml_clear_errors();
        $output = $dom->saveXML();

        // DOMDocument uses 2-space indent; adjust if needed
        if ($indent !== 2) {
            $output = $this->adjustIndent($output, 2, $indent);
        }

        return ['valid' => true, 'output' => $output];
    }

    private function formatHtml(string $input, int $indent): array
    {
        libxml_use_internal_errors(true);

        $dom                    = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput      = true;

        $loaded = @$dom->loadHTML($input, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        if (! $loaded) {
            libxml_clear_errors();
            return ['valid' => false, 'error' => 'invalid'];
        }

        libxml_clear_errors();
        $output = $dom->saveHTML();

        if ($output === false) {
            return ['valid' => false, 'error' => 'invalid'];
        }

        if ($indent !== 2) {
            $output = $this->adjustIndent($output, 2, $indent);
        }

        return ['valid' => true, 'output' => rtrim($output)];
    }

    private function adjustIndent(string $code, int $from, int $to): string
    {
        if ($from === $to || $from === 0) {
            return $code;
        }

        return preg_replace_callback(
            '/^( +)/m',
            function (array $m) use ($from, $to): string {
                $level = intdiv(strlen($m[1]), $from);
                return str_repeat(' ', $level * $to);
            },
            $code
        );
    }
}
