<?php

namespace App\Services;

use InvalidArgumentException;

class CodeImageService
{
    private const QR_BLOCKS = [
        1 => ['data' => 16, 'ecc' => 10, 'blocks' => [16]],
        2 => ['data' => 28, 'ecc' => 16, 'blocks' => [28]],
        3 => ['data' => 44, 'ecc' => 26, 'blocks' => [44]],
        4 => ['data' => 64, 'ecc' => 18, 'blocks' => [32, 32]],
    ];

    private const CODE_128_PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112',
    ];

    public function qrSvg(string $value): string
    {
        $config = $this->qrConfig(strlen($value));
        $codewords = $this->qrCodewords($value, $config);
        $version = $config['version'];
        $size = 17 + 4 * $version;
        $modules = array_fill(0, $size, array_fill(0, $size, false));
        $isFunction = array_fill(0, $size, array_fill(0, $size, false));

        $this->drawQrFunctionPatterns($modules, $isFunction, $version);
        $this->drawQrFormatBits($modules, $isFunction, 0);
        $this->drawQrCodewords($modules, $isFunction, $codewords);

        $bestModules = $modules;
        $bestPenalty = PHP_INT_MAX;

        for ($mask = 0; $mask < 8; $mask++) {
            $testModules = $modules;
            $this->applyQrMask($testModules, $isFunction, $mask);
            $this->drawQrFormatBits($testModules, $isFunction, $mask);
            $penalty = $this->qrPenalty($testModules);

            if ($penalty < $bestPenalty) {
                $bestPenalty = $penalty;
                $bestModules = $testModules;
            }
        }

        return $this->qrModulesToSvg($bestModules, 4);
    }

    public function barcodeSvg(string $value): string
    {
        if ($value === '' || preg_match('/[^\x20-\x7E]/', $value)) {
            throw new InvalidArgumentException('Kode buku harus berisi karakter ASCII yang dapat dicetak.');
        }

        $codes = [104];
        $checksum = 104;

        foreach (str_split($value) as $position => $character) {
            $code = ord($character) - 32;
            $codes[] = $code;
            $checksum += $code * ($position + 1);
        }

        $codes[] = $checksum % 103;
        $codes[] = 106;

        $moduleWidth = 2;
        $height = 72;
        $quietZone = 20;
        $x = $quietZone;
        $rects = [];

        foreach ($codes as $code) {
            $bar = true;
            foreach (str_split(self::CODE_128_PATTERNS[$code]) as $width) {
                $segmentWidth = (int) $width * $moduleWidth;

                if ($bar) {
                    $rects[] = '<rect x="'.$x.'" y="0" width="'.$segmentWidth.'" height="'.$height.'"/>';
                }

                $x += $segmentWidth;
                $bar = ! $bar;
            }
        }

        $width = $x + $quietZone;
        $label = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.$height.'" role="img" aria-label="Barcode '.$label.'" preserveAspectRatio="none"><rect width="'.$width.'" height="'.$height.'" fill="white"/><g fill="black">'.implode('', $rects).'</g></svg>';
    }

    private function qrConfig(int $length): array
    {
        foreach (self::QR_BLOCKS as $version => $config) {
            $capacity = intdiv($config['data'] * 8 - 12, 8);

            if ($length <= $capacity) {
                return ['version' => $version] + $config;
            }
        }

        throw new InvalidArgumentException('Kode anggota terlalu panjang untuk QR Code.');
    }

    private function qrCodewords(string $value, array $config): array
    {
        $bits = [];
        $this->appendBits($bits, 0b0100, 4);
        $this->appendBits($bits, strlen($value), 8);

        foreach (str_split($value) as $character) {
            $this->appendBits($bits, ord($character), 8);
        }

        $capacityBits = $config['data'] * 8;
        $this->appendBits($bits, 0, min(4, $capacityBits - count($bits)));

        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        $data = [];
        foreach (array_chunk($bits, 8) as $chunk) {
            $byte = 0;
            foreach ($chunk as $bit) {
                $byte = ($byte << 1) | $bit;
            }
            $data[] = $byte;
        }

        for ($padByte = 0xEC; count($data) < $config['data']; $padByte ^= 0xEC ^ 0x11) {
            $data[] = $padByte;
        }

        $dataBlocks = [];
        $eccBlocks = [];
        $offset = 0;

        foreach ($config['blocks'] as $blockLength) {
            $block = array_slice($data, $offset, $blockLength);
            $dataBlocks[] = $block;
            $eccBlocks[] = $this->reedSolomonRemainder($block, $config['ecc']);
            $offset += $blockLength;
        }

        $result = [];
        $maxDataLength = max($config['blocks']);

        for ($i = 0; $i < $maxDataLength; $i++) {
            foreach ($dataBlocks as $block) {
                if ($i < count($block)) {
                    $result[] = $block[$i];
                }
            }
        }

        for ($i = 0; $i < $config['ecc']; $i++) {
            foreach ($eccBlocks as $block) {
                $result[] = $block[$i];
            }
        }

        return $result;
    }

    private function appendBits(array &$bits, int $value, int $length): void
    {
        for ($i = $length - 1; $i >= 0; $i--) {
            $bits[] = ($value >> $i) & 1;
        }
    }

    private function reedSolomonRemainder(array $data, int $degree): array
    {
        $divisor = $this->reedSolomonDivisor($degree);
        $result = array_fill(0, $degree, 0);

        foreach ($data as $byte) {
            $factor = $byte ^ $result[0];
            array_shift($result);
            $result[] = 0;

            foreach ($divisor as $i => $coefficient) {
                $result[$i] ^= $this->galoisMultiply($coefficient, $factor);
            }
        }

        return $result;
    }

    private function reedSolomonDivisor(int $degree): array
    {
        $result = array_fill(0, $degree, 0);
        $result[$degree - 1] = 1;
        $root = 1;

        for ($i = 0; $i < $degree; $i++) {
            foreach ($result as $j => $coefficient) {
                $result[$j] = $this->galoisMultiply($coefficient, $root);

                if ($j + 1 < $degree) {
                    $result[$j] ^= $result[$j + 1];
                }
            }

            $root = $this->galoisMultiply($root, 0x02);
        }

        return $result;
    }

    private function galoisMultiply(int $x, int $y): int
    {
        $z = 0;

        for ($i = 7; $i >= 0; $i--) {
            $z = (($z << 1) ^ (($z >> 7) * 0x11D)) & 0xFF;
            $z ^= (($y >> $i) & 1) * $x;
        }

        return $z;
    }

    private function drawQrFunctionPatterns(array &$modules, array &$isFunction, int $version): void
    {
        $size = count($modules);
        $this->drawFinderPattern($modules, $isFunction, 0, 0);
        $this->drawFinderPattern($modules, $isFunction, $size - 7, 0);
        $this->drawFinderPattern($modules, $isFunction, 0, $size - 7);

        if ($version > 1) {
            $center = 4 * $version + 10;
            $this->drawAlignmentPattern($modules, $isFunction, $center, $center);
        }

        for ($i = 8; $i < $size - 8; $i++) {
            $dark = $i % 2 === 0;
            $this->setQrModule($modules, $isFunction, 6, $i, $dark, true);
            $this->setQrModule($modules, $isFunction, $i, 6, $dark, true);
        }

        $this->setQrModule($modules, $isFunction, 8, $size - 8, true, true);
    }

    private function drawFinderPattern(array &$modules, array &$isFunction, int $left, int $top): void
    {
        $size = count($modules);

        for ($dy = -1; $dy <= 7; $dy++) {
            for ($dx = -1; $dx <= 7; $dx++) {
                $x = $left + $dx;
                $y = $top + $dy;

                if ($x < 0 || $x >= $size || $y < 0 || $y >= $size) {
                    continue;
                }

                $dark = $dx >= 0 && $dx <= 6 && $dy >= 0 && $dy <= 6
                    && ($dx === 0 || $dx === 6 || $dy === 0 || $dy === 6 || ($dx >= 2 && $dx <= 4 && $dy >= 2 && $dy <= 4));
                $this->setQrModule($modules, $isFunction, $x, $y, $dark, true);
            }
        }
    }

    private function drawAlignmentPattern(array &$modules, array &$isFunction, int $centerX, int $centerY): void
    {
        for ($dy = -2; $dy <= 2; $dy++) {
            for ($dx = -2; $dx <= 2; $dx++) {
                $distance = max(abs($dx), abs($dy));
                $this->setQrModule($modules, $isFunction, $centerX + $dx, $centerY + $dy, $distance !== 1, true);
            }
        }
    }

    private function drawQrFormatBits(array &$modules, array &$isFunction, int $mask): void
    {
        $size = count($modules);
        $data = $mask;
        $remainder = $data << 10;

        for ($i = 14; $i >= 10; $i--) {
            if ((($remainder >> $i) & 1) !== 0) {
                $remainder ^= 0x537 << ($i - 10);
            }
        }

        $bits = (($data << 10) | $remainder) ^ 0x5412;

        for ($i = 0; $i <= 5; $i++) {
            $this->setQrModule($modules, $isFunction, 8, $i, $this->bit($bits, $i), true);
        }

        $this->setQrModule($modules, $isFunction, 8, 7, $this->bit($bits, 6), true);
        $this->setQrModule($modules, $isFunction, 8, 8, $this->bit($bits, 7), true);
        $this->setQrModule($modules, $isFunction, 7, 8, $this->bit($bits, 8), true);

        for ($i = 9; $i < 15; $i++) {
            $this->setQrModule($modules, $isFunction, 14 - $i, 8, $this->bit($bits, $i), true);
        }

        for ($i = 0; $i < 8; $i++) {
            $this->setQrModule($modules, $isFunction, $size - 1 - $i, 8, $this->bit($bits, $i), true);
        }

        for ($i = 8; $i < 15; $i++) {
            $this->setQrModule($modules, $isFunction, 8, $size - 15 + $i, $this->bit($bits, $i), true);
        }

        $this->setQrModule($modules, $isFunction, 8, $size - 8, true, true);
    }

    private function drawQrCodewords(array &$modules, array $isFunction, array $codewords): void
    {
        $bits = [];

        foreach ($codewords as $codeword) {
            $this->appendBits($bits, $codeword, 8);
        }

        $size = count($modules);
        $bitIndex = 0;
        $upward = true;

        for ($right = $size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right--;
            }

            for ($vertical = 0; $vertical < $size; $vertical++) {
                $y = $upward ? $size - 1 - $vertical : $vertical;

                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;

                    if (! $isFunction[$y][$x]) {
                        $modules[$y][$x] = ($bits[$bitIndex] ?? 0) === 1;
                        $bitIndex++;
                    }
                }
            }

            $upward = ! $upward;
        }
    }

    private function applyQrMask(array &$modules, array $isFunction, int $mask): void
    {
        $size = count($modules);

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if (! $isFunction[$y][$x] && $this->qrMask($mask, $x, $y)) {
                    $modules[$y][$x] = ! $modules[$y][$x];
                }
            }
        }
    }

    private function qrMask(int $mask, int $x, int $y): bool
    {
        return match ($mask) {
            0 => ($x + $y) % 2 === 0,
            1 => $y % 2 === 0,
            2 => $x % 3 === 0,
            3 => ($x + $y) % 3 === 0,
            4 => (intdiv($y, 2) + intdiv($x, 3)) % 2 === 0,
            5 => (($x * $y) % 2 + ($x * $y) % 3) === 0,
            6 => (($x * $y) % 2 + ($x * $y) % 3) % 2 === 0,
            7 => (($x + $y) % 2 + ($x * $y) % 3) % 2 === 0,
        };
    }

    private function qrPenalty(array $modules): int
    {
        $size = count($modules);
        $penalty = 0;

        for ($y = 0; $y < $size; $y++) {
            $runColor = $modules[$y][0];
            $runLength = 1;

            for ($x = 1; $x < $size; $x++) {
                if ($modules[$y][$x] === $runColor) {
                    $runLength++;
                } else {
                    if ($runLength >= 5) {
                        $penalty += $runLength - 2;
                    }
                    $runColor = $modules[$y][$x];
                    $runLength = 1;
                }
            }

            if ($runLength >= 5) {
                $penalty += $runLength - 2;
            }
        }

        for ($x = 0; $x < $size; $x++) {
            $runColor = $modules[0][$x];
            $runLength = 1;

            for ($y = 1; $y < $size; $y++) {
                if ($modules[$y][$x] === $runColor) {
                    $runLength++;
                } else {
                    if ($runLength >= 5) {
                        $penalty += $runLength - 2;
                    }
                    $runColor = $modules[$y][$x];
                    $runLength = 1;
                }
            }

            if ($runLength >= 5) {
                $penalty += $runLength - 2;
            }
        }

        for ($y = 0; $y < $size - 1; $y++) {
            for ($x = 0; $x < $size - 1; $x++) {
                $color = $modules[$y][$x];

                if ($modules[$y][$x + 1] === $color && $modules[$y + 1][$x] === $color && $modules[$y + 1][$x + 1] === $color) {
                    $penalty += 3;
                }
            }
        }

        $dark = 0;
        foreach ($modules as $row) {
            foreach ($row as $module) {
                if ($module) {
                    $dark++;
                }
            }
        }

        $total = $size * $size;
        $penalty += intdiv(abs($dark * 20 - $total * 10), $total) * 10;

        return $penalty;
    }

    private function setQrModule(array &$modules, array &$isFunction, int $x, int $y, bool $dark, bool $function): void
    {
        $modules[$y][$x] = $dark;

        if ($function) {
            $isFunction[$y][$x] = true;
        }
    }

    private function bit(int $value, int $index): bool
    {
        return (($value >> $index) & 1) !== 0;
    }

    private function qrModulesToSvg(array $modules, int $border): string
    {
        $size = count($modules);
        $viewSize = $size + $border * 2;
        $rects = [];

        foreach ($modules as $y => $row) {
            foreach ($row as $x => $dark) {
                if ($dark) {
                    $rects[] = '<rect x="'.($x + $border).'" y="'.($y + $border).'" width="1" height="1"/>';
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$viewSize.' '.$viewSize.'" role="img" aria-label="QR Code" shape-rendering="crispEdges"><rect width="'.$viewSize.'" height="'.$viewSize.'" fill="white"/><g fill="black">'.implode('', $rects).'</g></svg>';
    }
}
