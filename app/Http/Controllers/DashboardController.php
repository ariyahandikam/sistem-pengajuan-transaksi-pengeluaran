<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Submission;
use App\Services\DashboardChartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardChartService $chartService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user->roleSlug;

        return match ($role) {
            'staff'    => $this->staffDashboard($user),
            'spv'      => $this->approverDashboard($user, Submission::STATUS_WAITING_SPV, 'spv'),
            'manager'  => $this->approverDashboard($user, Submission::STATUS_WAITING_MANAGER, 'manager'),
            'direktur' => $this->approverDashboard($user, Submission::STATUS_WAITING_DIREKTUR, 'direktur'),
            'finance'  => $this->financeDashboard(),
            'admin'    => $this->adminDashboard(),
            default    => view('dashboard'),
        };
    }

    private function adminDashboard(): View
    {
        return view('dashboard.admin', [
            // Admin sees system-wide stats inside the view directly for now
        ]);
    }

    private function staffDashboard($user): View
    {
        return view('dashboard.staff', [
            'totalPengajuan'   => Submission::forUser($user->id)->count(),
            'menungguApproval' => Submission::forUser($user->id)->whereNotIn('status', [
                Submission::STATUS_DRAFT,
                Submission::STATUS_PAID,
                Submission::STATUS_REJECTED,
            ])->count(),
            'ditolak' => Submission::forUser($user->id)->where('status', Submission::STATUS_REJECTED)->count(),
            'paid'    => Submission::forUser($user->id)->where('status', Submission::STATUS_PAID)->count(),
        ]);
    }

    private function approverDashboard($user, string $status, string $role): View
    {
        $period = request()->query('period', 'monthly');
        
        // SPV dan Manager hanya melihat submissions yang mereka approve/reject
        // Director dan Finance melihat total semua submissions
        $isDirectorOrFinance = in_array($role, ['direktur', 'finance']);
        
        if ($isDirectorOrFinance) {
            // Director dan Finance: melihat semua submissions
            $totalPengajuan = Submission::count();
            $ditolak = Submission::where('status', Submission::STATUS_REJECTED)->count();
            $paid = Submission::where('status', Submission::STATUS_PAID)->count();
        } else {
            // SPV dan Manager: hanya melihat submissions yang mereka approve/reject
            $approverSubmissionIds = Approval::where('user_id', $user->id)
                ->whereIn('status', [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])
                ->distinct()
                ->pluck('submission_id');
            
            $totalPengajuan = Submission::whereIn('id', $approverSubmissionIds)->count();
            $ditolak = Submission::whereIn('id', $approverSubmissionIds)
                                    ->where('status', Submission::STATUS_REJECTED)->count();
            $paid = Submission::whereIn('id', $approverSubmissionIds)
                                    ->where('status', Submission::STATUS_PAID)->count();
        }
        
        return view("dashboard.{$role}", [
            'totalPengajuan'   => $totalPengajuan,
            'menungguApproval' => Submission::where('status', $status)->count(),
            'ditolak'          => $ditolak,
            'paid'             => $paid,
            'submissionChart'  => $this->chartService->getSubmissionChartData($period),
            'categoryExpense'   => $this->chartService->getCategoryExpensePercentage(),
            'expenseReport'    => $this->chartService->getExpenseReport(),
            'activePeriod'     => $period,
        ]);
    }

    private function financeDashboard(): View
    {
        $period = request()->query('period', 'monthly');
        
        return view('dashboard.finance', [
            'totalPengajuan'   => Submission::count(),
            'menungguApproval' => Submission::where('status', Submission::STATUS_WAITING_FINANCE)->count(),
            'ditolak'          => Submission::where('status', Submission::STATUS_REJECTED)->count(),
            'paid'             => Submission::where('status', Submission::STATUS_PAID)->count(),
            'submissionChart'  => $this->chartService->getSubmissionChartData($period),
            'categoryExpense'   => $this->chartService->getCategoryExpensePercentage(),
            'expenseReport'    => $this->chartService->getExpenseReport(),
            'activePeriod'     => $period,
        ]);
    }
}

