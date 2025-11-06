<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/seed', [ProductController::class, 'seed']);
Route::get('/search', [ProductController::class, 'search']);
