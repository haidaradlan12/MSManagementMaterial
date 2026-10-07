<?php

namespace App\Exports;

use App\Models\StockOpname;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected string $dateFrom,
        protected string $dateTo,
    ) {}

    public function collection()
    {
        return StockOpname::with('material', 'user')
            ->whereBetween('opname_date', [$this->dateFrom, $this->dateTo])
            ->orderBy('opname_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Opname',
            'Nama Material',
            'Lokasi Penyimpanan',
            'Stok Sistem',
            'Stok Aktual',
            'Selisih',
            'Status',
            'Catatan',
            'Dicatat Oleh',
        ];
    }

    /** @param StockOpname $row */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        $status = match ($row->status) {
            'matched' => 'Sesuai',
            'surplus' => 'Surplus',
            'missing' => 'Kurang',
            default => $row->status,
        };

        return [
            $no,
            Carbon::parse($row->opname_date)->format('d/m/Y'),
            $row->material_label,
            $row->location ?? '-',
            $row->system_quantity,
            $row->actual_quantity,
            $row->difference,
            $status,
            $row->notes ?? '-',
            $row->user?->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row bold + background
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 16,
            'C' => 30,
            'D' => 22,
            'E' => 14,
            'F' => 14,
            'G' => 10,
            'H' => 12,
            'I' => 30,
            'J' => 20,
        ];
    }

    public function title(): string
    {
        return 'Stock Opname '
            .Carbon::parse($this->dateFrom)->format('d-m-Y')
            .' sd '
            .Carbon::parse($this->dateTo)->format('d-m-Y');
    }
}
