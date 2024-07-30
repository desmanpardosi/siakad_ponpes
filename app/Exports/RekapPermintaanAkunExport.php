<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class RekapPermintaanAkunExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithMapping, WithStyles
{
    protected $rows;
    protected $date_range;

    public function __construct(array $rows, $date_range)
    {
        $this->rows = $rows;
        $this->date_range = $date_range;
    }

    public function map($row): array
    {
        return [
            [
                $row->no,
                $row->req_date,
                $row->nama_group,
                $row->fullname,
                $row->keterangan
            ]
        ];
    }

    public function headings(): array
    {
        return [
            ["PERMINTAAN AKUN"],
            [$this->date_range],
            [
                'No.',
                'Tanggal',
                'Unit',
                'Nama',
                'Keterangan'
            ]
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Permintaan Akun';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        return [
            1    => [
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                        ]
                    ],
            2    => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                    ]
                ],
            3    => ['font' => ['bold' => true]],
        ];
    }
}