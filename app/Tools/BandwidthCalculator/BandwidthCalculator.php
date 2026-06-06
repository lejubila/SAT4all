<?php

namespace App\Tools\BandwidthCalculator;

class BandwidthCalculator
{
    // Bytes per unit (SI decimal)
    public const SIZE_UNITS = [
        'B'   => 1,
        'KB'  => 1_000,
        'MB'  => 1_000_000,
        'GB'  => 1_000_000_000,
        'TB'  => 1_000_000_000_000,
        'KiB' => 1_024,
        'MiB' => 1_048_576,
        'GiB' => 1_073_741_824,
        'TiB' => 1_099_511_627_776,
    ];

    // Bits per second per unit
    public const BW_UNITS = [
        'bps'  => 1,
        'Kbps' => 1_000,
        'Mbps' => 1_000_000,
        'Gbps' => 1_000_000_000,
    ];

    // Seconds per unit
    public const TIME_UNITS = [
        's'   => 1,
        'min' => 60,
        'h'   => 3_600,
        'day' => 86_400,
    ];

    /**
     * mode = 'time'      → compute transfer time
     * mode = 'size'      → compute transferable file size
     * mode = 'bandwidth' → compute required bandwidth
     */
    public function calculate(
        string $mode,
        float  $sizeValue,
        string $sizeUnit,
        float  $bwValue,
        string $bwUnit,
        float  $timeValue,
        string $timeUnit,
        float  $overhead = 0.0
    ): array {
        $overheadFactor = 1.0 + max(0.0, min(99.0, $overhead)) / 100.0;

        $sizeBytes = $sizeValue * (self::SIZE_UNITS[$sizeUnit] ?? 1);
        $bwBps     = $bwValue   * (self::BW_UNITS[$bwUnit]     ?? 1);
        $timeSec   = $timeValue * (self::TIME_UNITS[$timeUnit]  ?? 1);

        return match ($mode) {
            'time'      => $this->calcTime($sizeBytes, $bwBps, $overheadFactor),
            'size'      => $this->calcSize($bwBps, $timeSec, $overheadFactor),
            'bandwidth' => $this->calcBandwidth($sizeBytes, $timeSec, $overheadFactor),
            default     => ['valid' => false, 'error' => 'unknown_mode'],
        };
    }

    // --- calculation methods --------------------------------------------------

    private function calcTime(float $sizeBytes, float $bwBps, float $overhead): array
    {
        if ($sizeBytes <= 0 || $bwBps <= 0) {
            return ['valid' => false, 'error' => 'positive_required'];
        }

        $sizeBits   = $sizeBytes * 8 * $overhead;
        $seconds    = $sizeBits / $bwBps;
        $throughput = $bwBps / 8; // bytes per second

        return [
            'valid'  => true,
            'mode'   => 'time',
            'result' => [
                'seconds'  => $seconds,
                'label'    => $this->fmtTime($seconds),
                'breakdown'=> $this->timeBreakdown($seconds),
            ],
            'extras' => [
                'throughput_bytes' => $throughput,
                'size_bits'        => $sizeBits,
            ],
        ];
    }

    private function calcSize(float $bwBps, float $timeSec, float $overhead): array
    {
        if ($bwBps <= 0 || $timeSec <= 0) {
            return ['valid' => false, 'error' => 'positive_required'];
        }

        $bytes = ($bwBps / 8) * $timeSec / $overhead;

        return [
            'valid'  => true,
            'mode'   => 'size',
            'result' => [
                'bytes' => $bytes,
                'label' => $this->fmtSize($bytes),
            ],
            'extras' => [
                'throughput_bytes' => $bwBps / 8,
            ],
        ];
    }

    private function calcBandwidth(float $sizeBytes, float $timeSec, float $overhead): array
    {
        if ($sizeBytes <= 0 || $timeSec <= 0) {
            return ['valid' => false, 'error' => 'positive_required'];
        }

        $bps = ($sizeBytes * 8 * $overhead) / $timeSec;

        return [
            'valid'  => true,
            'mode'   => 'bandwidth',
            'result' => [
                'bps'   => $bps,
                'label' => $this->fmtBandwidth($bps),
            ],
        ];
    }

    // --- formatting helpers ---------------------------------------------------

    public function fmtTime(float $seconds): string
    {
        if ($seconds < 0.001) {
            return number_format($seconds * 1_000_000, 1) . ' µs';
        }
        if ($seconds < 1) {
            return number_format($seconds * 1_000, 2) . ' ms';
        }
        if ($seconds < 60) {
            return number_format($seconds, 2) . ' s';
        }
        if ($seconds < 3_600) {
            $m = (int) ($seconds / 60);
            $s = (int) fmod($seconds, 60);
            return "{$m} min {$s} s";
        }
        if ($seconds < 86_400) {
            $h = (int) ($seconds / 3_600);
            $m = (int) (fmod($seconds, 3_600) / 60);
            return "{$h} h {$m} min";
        }
        $d = (int) ($seconds / 86_400);
        $h = (int) (fmod($seconds, 86_400) / 3_600);
        return "{$d} d {$h} h";
    }

    public function fmtSize(float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $i     = 0;
        while ($bytes >= 1_000 && $i < count($units) - 1) {
            $bytes /= 1_000;
            $i++;
        }
        return number_format($bytes, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }

    public function fmtBandwidth(float $bps): string
    {
        $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
        $i     = 0;
        while ($bps >= 1_000 && $i < count($units) - 1) {
            $bps /= 1_000;
            $i++;
        }
        return number_format($bps, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }

    public function sizeTable(float $bytes): array
    {
        $rows = [];
        foreach (['B' => 1, 'KB' => 1_000, 'MB' => 1_000_000, 'GB' => 1_000_000_000, 'TB' => 1_000_000_000_000] as $u => $div) {
            $rows[] = ['unit' => $u, 'value' => number_format($bytes / $div, $div === 1 ? 0 : 4)];
        }
        return $rows;
    }

    public function bwTable(float $bps): array
    {
        $rows = [];
        foreach (self::BW_UNITS as $u => $div) {
            $rows[] = ['unit' => $u, 'value' => number_format($bps / $div, 4)];
        }
        return $rows;
    }

    public function timeBreakdown(float $seconds): array
    {
        return [
            's'   => number_format($seconds, 2),
            'min' => number_format($seconds / 60, 4),
            'h'   => number_format($seconds / 3_600, 6),
            'day' => number_format($seconds / 86_400, 8),
        ];
    }
}
