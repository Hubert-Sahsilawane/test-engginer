<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ProfitLossRequest;
use App\Exports\ProfitLossExport;
use App\Http\Resources\Laporan\ProfitLossResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function profitLoss(ProfitLossRequest $request)
    {
        try {
            $bulan = $request->bulan;

            $transaksi = Transaksi::with('coa.kategori')
                ->whereMonth('tanggal', date('m', strtotime($bulan . '-01')))
                ->whereYear('tanggal', date('Y', strtotime($bulan . '-01')))
                ->get();

            if ($transaksi->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tidak ada data transaksi pada bulan ini.',
                ], 404);
            }

            $data = $transaksi->groupBy('coa.kategori.nama')->map(function ($items, $kategori) {
                return [
                    'kategori' => $kategori,
                    'total' => $items->sum('credit') - $items->sum('debit'),
                ];
            })->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan profit & loss berhasil dibuat.',
                'data' => ProfitLossResource::collection($data),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
{
    try {
        // Ambil bulanList dari query string (contoh: ?bulanList=2025-01,2025-02,2025-03)
        $bulanListParam = $request->query('bulanList');

        if (!$bulanListParam) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Parameter bulanList wajib diisi, contoh: ?bulanList=2025-01,2025-02,2025-03',
            ], 422);
        }

        // Pisahkan jadi array
        $bulanList = explode(',', $bulanListParam);

        $data = [
            'income' => [],
            'expense' => [],
            'total_income' => [],
            'total_expense' => [],
        ];

        foreach ($bulanList as $bulan) {
            $transaksi = Transaksi::with('coa.kategori')
                ->whereMonth('tanggal', date('m', strtotime($bulan . '-01')))
                ->whereYear('tanggal', date('Y', strtotime($bulan . '-01')))
                ->get();

            foreach ($transaksi->groupBy('coa.kategori.nama') as $kategori => $items) {
                $total = $items->sum('credit') - $items->sum('debit');

                if ($total >= 0) {
                    $data['income'][$kategori][$bulan] = $total;
                    $data['total_income'][$bulan] = ($data['total_income'][$bulan] ?? 0) + $total;
                } else {
                    $data['expense'][$kategori][$bulan] = abs($total);
                    $data['total_expense'][$bulan] = ($data['total_expense'][$bulan] ?? 0) + abs($total);
                }
            }
        }

        // Download file Excel
        return Excel::download(
            new ProfitLossExport($data, $bulanList),
            'Laporan_Profit_Loss.xlsx'
        );
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'failed',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
