<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $data;
    protected $bulanList;

    public function __construct($data, $bulanList)
    {
        $this->data = $data;
        $this->bulanList = $bulanList;
    }

    public function headings(): array
    {
        return array_merge(['Kategori'], $this->bulanList);
    }

    public function array(): array
    {
        $rows = [];

        // Income
        foreach ($this->data['income'] as $kategori => $values) {
            $row = [$kategori];
            foreach ($this->bulanList as $bulan) {
                $row[] = $values[$bulan] ?? 0;
            }
            $rows[] = $row;
        }

        // Total Income
        $rows[] = array_merge(['Total Income'], array_map(fn($b) => $this->data['total_income'][$b] ?? 0, $this->bulanList));

        // Expense
        foreach ($this->data['expense'] as $kategori => $values) {
            $row = [$kategori];
            foreach ($this->bulanList as $bulan) {
                $row[] = $values[$bulan] ?? 0;
            }
            $rows[] = $row;
        }

        // Total Expense
        $rows[] = array_merge(['Total Expense'], array_map(fn($b) => $this->data['total_expense'][$b] ?? 0, $this->bulanList));

        // Net Income
        $rows[] = array_merge(['Net Income'], array_map(
            fn($b) => ($this->data['total_income'][$b] ?? 0) - ($this->data['total_expense'][$b] ?? 0),
            $this->bulanList
        ));

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);
    }
}
