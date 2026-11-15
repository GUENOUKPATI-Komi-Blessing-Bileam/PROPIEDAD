<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.dashboard')->name('dashboard');
Route::view('/assets', 'pages.assets.index')->name('assets.index');
Route::view('/assets/create', 'pages.assets.create')->name('assets.create');
Route::view('/assets/{asset}', 'pages.assets.show')->name('assets.show');
Route::view('/movements', 'pages.movements.index')->name('movements.index');
Route::view('/movements/create', 'pages.movements.create')->name('movements.create');
Route::view('/inventories/2026', 'pages.inventory')->name('inventory.show');
