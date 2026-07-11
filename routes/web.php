<?php

use App\Http\Controllers\Approver\ApprovalController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route khusus Staff
    Route::middleware('role:staff')->group(function () {
        Route::resource('submissions', SubmissionController::class, ['except' => ['show']]);
    });

    // Public route untuk view submission detail (untuk email links)
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');

    // Route Global untuk Download & Lihat Lampiran
    Route::get('/submissions/{submission}/download/{index}', [SubmissionController::class, 'downloadAttachment'])
        ->name('submissions.download');
    Route::get('/submissions/{submission}/view/{index}', [SubmissionController::class, 'viewAttachment'])
        ->name('submissions.view');

    // Route untuk Approver (SPV, Manager, Direktur, Finance)
    Route::middleware('role:spv,manager,direktur,finance')->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');

        // Approval History (Riwayat Persetujuan)
        Route::get('approvals/history', [\App\Http\Controllers\Approver\ApprovalHistoryController::class, 'index'])
            ->name('approvals.history.index');
        Route::get('approvals/history/{submission}', [\App\Http\Controllers\Approver\ApprovalHistoryController::class, 'show'])
            ->name('approvals.history.show');

        Route::get('approvals/{submission}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('approvals/{submission}/process', [ApprovalController::class, 'process'])->name('approvals.process');

        // Reports (Direktur & Finance)
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('reports.export');
        Route::get('reports/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('reports/export/excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export.excel');
        
        // Budget (Finance only)
        Route::middleware('role:finance')->group(function () {
            Route::get('budgets', [BudgetController::class, 'index'])->name('budgets.index');
            Route::get('budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
            Route::get('budgets/info', [BudgetController::class, 'getBudgetInfo'])->name('budgets.info');
            Route::post('budgets', [BudgetController::class, 'store'])->name('budgets.store');
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        });
    });

    // Admin (System Administrator) - not part of approval workflow
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activitylogs.index');
        Route::get('activity-logs/{activity}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activitylogs.show');

        // Audit Trail
        Route::get('audit-trail', [\App\Http\Controllers\Admin\AuditTrailController::class, 'index'])->name('audit-trail.index');
        Route::get('audit-trail/export', [\App\Http\Controllers\Admin\AuditTrailController::class, 'exportCsv'])->name('audit-trail.export');
        Route::get('audit-trail/{activity}', [\App\Http\Controllers\Admin\AuditTrailController::class, 'show'])->name('audit-trail.show');
    });
});

require __DIR__.'/auth.php';
