<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\CoaRequest;
use App\Http\Resources\CoaResource;
use App\Models\Coa;

class CoaController extends Controller
{
    public function index()
    {
        try {
            DB::beginTransaction();

            $coa = Coa::with('kategori')->get();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data COA berhasil diambil.',
                'data' => CoaResource::collection($coa)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(CoaRequest $request)
    {
        try {
            DB::beginTransaction();

            $coa = Coa::create($request->validated());

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'COA berhasil ditambahkan.',
                'data' => new CoaResource($coa)
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(CoaRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $coa = Coa::findOrFail($id);
            $coa->update($request->validated());

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'COA berhasil diperbarui.',
                'data' => new CoaResource($coa)
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Data COA tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $coa = Coa::findOrFail($id);
            $coa->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'COA berhasil dihapus.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Data COA tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}
