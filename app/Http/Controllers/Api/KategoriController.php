<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\KategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        try {
            DB::beginTransaction();

            $kategori = Kategori::all();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data kategori berhasil diambil.',
                'data' => KategoriResource::collection($kategori)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(KategoriRequest $request)
    {
        try {
            DB::beginTransaction();

            $kategori = Kategori::create($request->validated());

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => new KategoriResource($kategori)
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(KategoriRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $kategori = Kategori::findOrFail($id);
            $kategori->update($request->validated());

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil diperbarui.',
                'data' => new KategoriResource($kategori)
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

            $kategori = Kategori::findOrFail($id);
            $kategori->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }
}
