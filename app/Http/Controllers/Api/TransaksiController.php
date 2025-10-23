<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\TransaksiRequest;
use App\Http\Resources\TransaksiResource;
use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function index()
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::with('coa.kategori')->get();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data transaksi berhasil diambil.',
                'data' => TransaksiResource::collection($transaksi)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(TransaksiRequest $request)
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::create($request->validated());

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil ditambahkan.',
                'data' => new TransaksiResource($transaksi)
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(TransaksiRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::findOrFail($id);
            $transaksi->update($request->validated());

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil diperbarui.',
                'data' => new TransaksiResource($transaksi)
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::findOrFail($id);
            $transaksi->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }
}
