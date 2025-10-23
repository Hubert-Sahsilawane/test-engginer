<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
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
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
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

            $coa = Coa::findOrFail($id);
            $coa->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'COA berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }
}
