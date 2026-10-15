<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private readonly Collection $rows,
        private readonly array $meta,
        private readonly string $period,
        private readonly string $printedAt,
        private readonly int $total,
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            ['SMP Negeri 6 Bandung'],
            ['Sistem Informasi Perpustakaan'],
            ['Jenis Laporan', $this->meta['title']],
            ['Periode', $this->period],
            ['Tanggal Cetak', $this->printedAt],
            ['Total Data', number_format($this->total, 0, ',', '.').' data'],
            [],
            array_values($this->meta['columns']),
        ];
    }

    public function map(mixed $row): array
    {
        return collect(array_keys($this->meta['columns']))
            ->map(fn (string $key): string => (string) ($row[$key] ?? '-'))
            ->all();
    }

    public function title(): string
    {
        return str($this->meta['title'])
            ->replace('Laporan ', '')
            ->limit(31, '')
            ->toString();
    }
}
