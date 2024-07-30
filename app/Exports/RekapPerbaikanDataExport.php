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

class RekapPerbaikanDataExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithMapping, WithStyles
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
            $row->no,
            $row->req_date,
            $row->nama_group,
            $row->fullname,
            $row->tgl_kejadian,
            $row->module,
            $row->kronologis
        ];
    }

    public function headings(): array
    {
        return [
            ["PERMINTAAN PERBAIKAN DATA"],
            [$this->date_range],
            [
                'No.',
                'Tanggal Permintaan',
                'Unit',
                'Nama',
                'Tanggal Kejadian',
                'Module',
                'Kronologis'
            ]
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Perbaikan Data';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:G1')->getAlignment()->applyFromArray(
            array('horizontal' => 'center')
        );
        $sheet->getStyle('A2:G2')->getAlignment()->applyFromArray(
            array('horizontal' => 'center')
        );

        return [
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
        ];
    }
}