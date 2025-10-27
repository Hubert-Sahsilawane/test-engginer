    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\KategoriController;
    use App\Http\Controllers\Api\CoaController;
    use App\Http\Controllers\Api\TransaksiController;
    use App\Http\Controllers\Api\LaporanController;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    | Semua endpoint di bawah ini bisa langsung kamu test di Postman
    | tanpa harus login atau middleware dulu.
    |--------------------------------------------------------------------------
    */

    Route::prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index']);
        Route::post('/', [KategoriController::class, 'store']);
        Route::put('/{id}', [KategoriController::class, 'update']);
        Route::delete('/{id}', [KategoriController::class, 'destroy']);
    });

    Route::prefix('coa')->group(function () {
        Route::get('/', [CoaController::class, 'index']);
        Route::post('/', [CoaController::class, 'store']);
        Route::put('/{id}', [CoaController::class, 'update']);
        Route::delete('/{id}', [CoaController::class, 'destroy']);
    });

    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index']);
        Route::post('/', [TransaksiController::class, 'store']);
        Route::put('/{id}', [TransaksiController::class, 'update']);
        Route::delete('/{id}', [TransaksiController::class, 'destroy']);
    });

    Route::prefix('laporan')->group(function () {
    Route::get('/transaksi', [LaporanController::class, 'laporanTransaksi']);
    Route::get('/profit-loss/export', [LaporanController::class, 'exportExcel']);
});

