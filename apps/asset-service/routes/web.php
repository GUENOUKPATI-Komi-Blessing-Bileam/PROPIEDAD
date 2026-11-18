<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovementController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.dashboard')->name('dashboard');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
Route::get('/movements/create', [MovementController::class, 'create'])->name('movements.create');
Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
Route::view('/inventories/2026', 'pages.inventory')->name('inventory.show');
