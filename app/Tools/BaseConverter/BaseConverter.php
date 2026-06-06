<?php

namespace App\Tools\BaseConverter;

class BaseConverter
{
    private const CHARS = '0123456789abcdefghijklmnopqrstuvwxyz';

    public function convert(string $input, int $fromBase): array
    {
        $input = trim($input);

        if ($input === '' || $input === '-') {
            return ['idle' => true];
        }

        $negative = str_starts_with($input, '-');
        $raw      = $negative ? substr($input, 1) : $input;
        $number   = strtolower(ltrim($raw, '0')) ?: '0';

        if (! $this->isValidForBase($number, $fromBase)) {
            return ['idle' => false, 'valid' => false, 'error' => 'invalid_chars'];
        }

        $decimal = $this->toDecimal($number, $fromBase);
        if ($decimal === null) {
            return ['idle' => false, 'valid' => false, 'error' => 'overflow'];
        }

        $signed = $negative ? -$decimal : $decimal;

        $results = [];
        foreach ([2 => 'BIN', 8 => 'OCT', 10 => 'DEC', 16 => 'HEX'] as $base => $label) {
            $results[] = [
                'base'  => $base,
                'label' => $label,
                'value' => $this->fromDecimal($signed, $base),
            ];
        }

        $bitLength = $decimal > 0 ? (int) floor(log($decimal, 2)) + 1 : 1;

        $extras = ['bit_length' => $bitLength];
        if (! $negative && $decimal >= 32 && $decimal <= 126) {
            $extras['ascii'] = chr((int) $decimal);
        }

        return [
            'idle'    => false,
            'valid'   => true,
            'error'   => null,
            'input'   => ['value' => $input, 'base' => $fromBase],
            'results' => $results,
            'extras'  => $extras,
        ];
    }

    private function isValidForBase(string $number, int $base): bool
    {
        $chars = substr(self::CHARS, 0, $base);
        return (bool) preg_match('/^[' . preg_quote($chars, '/') . ']+$/', $number);
    }

    private function toDecimal(string $number, int $fromBase): ?int
    {
        $result = 0;
        $len    = strlen($number);

        for ($i = 0; $i < $len; $i++) {
            $digit = strpos(self::CHARS, $number[$i]);
            if ($digit === false || $digit >= $fromBase) {
                return null;
            }
            // Overflow guard before multiply
            if ($result > intdiv(PHP_INT_MAX - $digit, $fromBase)) {
                return null;
            }
            $result = $result * $fromBase + $digit;
        }

        return $result;
    }

    private function fromDecimal(int $decimal, int $toBase): string
    {
        $negative = $decimal < 0;
        $abs      = abs($decimal);

        $value = match ($toBase) {
            2       => decbin($abs),
            8       => decoct($abs),
            10      => (string) $abs,
            16      => strtoupper(dechex($abs)),
            default => strtoupper(base_convert((string) $abs, 10, $toBase)),
        };

        return $negative ? '-' . $value : $value;
    }
}
