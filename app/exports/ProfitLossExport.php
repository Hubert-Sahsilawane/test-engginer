<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $bulanList;

    public function __construct($data, $bulanList)
    {
        $this->data = $data;
        $this->bulanList = $bulanList;
    }

    public function view(): View
    {
        return view('exports.profit_loss', [
            'data' => $this->data,
            'bulanList' => $this->bulanList
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z2')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => 'FFFF00']
            ],
            'alignment' => ['horizontal' => 'center']
        ]);
    }
}
