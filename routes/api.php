<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua Controller yang baru
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;

// ==========================================
// RUTE AUTENTIKASI (KEYCLOAK & LOKAL)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// RUTE USER / AKUN
// ==========================================
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);

// ==========================================
// RUTE KELAS
// ==========================================
Route::get('/wali-kelas', [KelasController::class, 'listWaliKelas']); 
Route::get('/kelas', [KelasController::class, 'index']);
Route::post('/kelas', [KelasController::class, 'store']);
Route::get('/kelas/{id}', [KelasController::class, 'show']);
Route::put('/kelas/{id}', [KelasController::class, 'update']);

// ==========================================
// RUTE SISWA
// ==========================================
Route::get('/siswas', [SiswaController::class, 'index']);
Route::post('/siswas', [SiswaController::class, 'store']);
Route::get('/siswas/{id}', [SiswaController::class, 'show']);
Route::put('/siswas/{id}', [SiswaController::class, 'update']);