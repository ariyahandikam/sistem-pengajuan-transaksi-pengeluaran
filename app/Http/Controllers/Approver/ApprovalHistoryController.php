<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Submission;
use Illuminate\Http\Request;

class ApprovalHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->roleSlug;

        $query = Approval::with(['submission.user', 'submission.category', 'user'])
            ->whereIn('status', [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]);

        // Role-specific rules
        if ($role === 'spv' || $role === 'manager' || $role === 'finance') {
            $query->where('user_id', $user->id)->where('role', $role);
        }

        // Filters
        if ($search = $request->query('q')) {
            $query->whereHas('submission', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->query('category')) {
            $query->whereHas('submission', fn($q) => $q->where('category_id', $category));
        }

        if ($from = $request->query('from')) {
            $query->whereDate('approved_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('approved_at', '<=', $to);
        }

        $approvals = $query->orderByDesc('approved_at')->paginate(15)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('approvals.history.index', compact('approvals', 'categories'));
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['user', 'category']);
        $approvals = Approval::with('user')->where('submission_id', $submission->id)
            ->orderBy('approved_at')->get();

        return view('approvals.history.show', compact('submission', 'approvals'));
    }
}
