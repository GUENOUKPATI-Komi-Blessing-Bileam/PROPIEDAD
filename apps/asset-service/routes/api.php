<?php

use App\Http\Controllers\Api\AssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/assets/references', [AssetController::class, 'references']);
    Route::get('/assets', [AssetController::class, 'index']);
    Route::post('/assets', [AssetController::class, 'store']);
    Route::get('/assets/{assetId}', [AssetController::class, 'show']);
});
