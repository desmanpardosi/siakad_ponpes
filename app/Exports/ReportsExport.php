<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportsExport implements FromArray, WithMultipleSheets
{
    use Exportable;
    protected $sheets;

    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [
            new RekapPermintaanAkunExport($this->sheets['permintaan_akun'], $this->sheets['date_range']),
            new RekapPerbaikanDataExport($this->sheets['perbaikan_data'], $this->sheets['date_range'])
        ];

        return $sheets;
    }
}
