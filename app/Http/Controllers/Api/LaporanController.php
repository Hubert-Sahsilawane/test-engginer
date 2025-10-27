<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitLossExport;

class LaporanController extends Controller
{
    /**
     * Ambil laporan transaksi berdasarkan rentang tanggal
     */
    public function laporanTransaksi(Request $request)
    {
        try {
            $tanggalAwal = $request->query('tanggal_awal');
            $tanggalAkhir = $request->query('tanggal_akhir');

            if (!$tanggalAwal || !$tanggalAkhir) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Parameter tanggal_awal dan tanggal_akhir wajib diisi.'
                ], 422);
            }

            $transaksi = Transaksi::with(['coa.kategori'])
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($transaksi->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tidak ada data transaksi pada rentang tanggal ini.'
                ], 404);
            }

            // --- Tambahan: ringkasan total debit & credit ---
            $totalDebit = $transaksi->sum('debit');
            $totalCredit = $transaksi->sum('credit');

            // --- Tambahan: hitung laba/rugi ---
            $profitLoss = $totalCredit - $totalDebit;

            // --- Tambahan: kelompokkan per kategori ---
            $kategoriSummary = $transaksi->groupBy('coa.kategori.nama')->map(function ($items) {
                return [
                    'total_debit' => $items->sum('debit'),
                    'total_credit' => $items->sum('credit'),
                    'selisih' => $items->sum('credit') - $items->sum('debit'),
                    'transaksi' => $items->map(function ($t) {
                        return [
                            'tanggal' => $t->tanggal,
                            'keterangan' => $t->keterangan,
                            'coa' => $t->coa->nama ?? '-',
                            'debit' => $t->debit,
                            'credit' => $t->credit,
                        ];
                    })->values()
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan transaksi berhasil diambil.',
                'data' => [
                    'periode' => [
                        'tanggal_awal' => $tanggalAwal,
                        'tanggal_akhir' => $tanggalAkhir
                    ],
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'profit_loss' => $profitLoss,
                    'kategori_summary' => $kategoriSummary,
                    'transaksi_detail' => $transaksi
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export laporan profit & loss ke Excel berdasarkan daftar bulan
     */
    public function exportExcel(Request $request)
    {
        try {
            $bulanListParam = $request->query('bulanList');

            if (!$bulanListParam) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Parameter bulanList wajib diisi, contoh: ?bulanList=2025-01,2025-02'
                ], 422);
            }

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

                if ($transaksi->isEmpty()) continue;

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

            if (empty($data['income']) && empty($data['expense'])) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tidak ada data transaksi pada bulan-bulan yang diminta.'
                ], 404);
            }

            return Excel::download(
                new ProfitLossExport($data, $bulanList),
                'Laporan_Profit_Loss.xlsx'
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}
