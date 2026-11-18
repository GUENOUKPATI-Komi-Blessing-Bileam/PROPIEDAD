<?php

use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', GatewayController::class)->name('gateway.auth.login');
    Route::post('auth/logout', GatewayController::class)->name('gateway.auth.logout');
    Route::get('auth/me', GatewayController::class)->name('gateway.auth.me');

    Route::match(['GET', 'POST'], 'categories', GatewayController::class)->name('gateway.categories');
    Route::match(['GET', 'POST'], 'assets', GatewayController::class)->name('gateway.assets.index');
    Route::match(['GET', 'PATCH'], 'assets/{assetId}', GatewayController::class)->name('gateway.assets.show');

    Route::get('movements', GatewayController::class)->name('gateway.movements.index');
    Route::post('movements', GatewayController::class)->name('gateway.movements.store');
    Route::post('movements/{movementId}/approve', GatewayController::class)->name('gateway.movements.approve');
    Route::post('movements/{movementId}/reject', GatewayController::class)->name('gateway.movements.reject');

    Route::match(['GET', 'POST'], 'inventory-campaigns', GatewayController::class)->name('gateway.inventory.index');
    Route::get('inventory-campaigns/{campaignId}', GatewayController::class)->name('gateway.inventory.show');
    Route::post('inventory-campaigns/{campaignId}/checks', GatewayController::class)->name('gateway.inventory.checks');

    Route::get('history', GatewayController::class)->name('gateway.history.index');

    Route::get('reports/summary', GatewayController::class)->name('gateway.reports.summary');
    Route::get('reports/assets.csv', GatewayController::class)->name('gateway.reports.assets');
});
