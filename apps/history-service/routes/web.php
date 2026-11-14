<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'history.index')->name('history.index');
Route::redirect('/history', '/');
