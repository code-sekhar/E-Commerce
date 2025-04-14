<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/category', [CategoryController::class, 'store']);
});
Route::middleware(['auth:sanctum', 'role:seller'])->group(function () {
    
});
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/category', [CategoryController::class, 'index']);
});
//All get 
