<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint API untuk Sistem Pengajuan Transaksi Pengeluaran.
| Semua endpoint di-prefix dengan /api secara otomatis oleh Laravel.
|
| Autentikasi: Menggunakan Laravel Sanctum (Bearer Token).
|
*/

// ─── Public Routes (Tidak perlu login) ──────────────────────────────

Route::post('/login', [AuthController::class, 'login']);

// ─── Protected Routes (Wajib login dengan Bearer Token) ─────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Kategori (untuk dropdown form pengajuan)
    Route::get('/categories', [SubmissionController::class, 'categories']);

    // Pengajuan (Staff)
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);

    // Persetujuan (Approver: SPV, Manager, Direktur, Finance)
    Route::get('/approvals/pending', [ApprovalController::class, 'pending']);
    Route::post('/approvals/{submission}/process', [ApprovalController::class, 'process']);
});
