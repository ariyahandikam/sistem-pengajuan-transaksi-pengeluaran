<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Submission;
use App\Services\SubmissionService;
use App\Services\WorkflowService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionService $service,
        protected WorkflowService $workflowService
    ) {}

    /**
     * Daftar pengajuan milik user yang sedang login.
     *
     * GET /api/submissions?status=draft&per_page=15
     */
    public function index(Request $request): JsonResponse
    {
        $query = Submission::with(['category', 'approvals.user'])
            ->where('user_id', $request->user()->id)
            ->latest('id');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = min($request->integer('per_page', 15), 50);
        $submissions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $submissions->through(fn ($s) => $this->formatSubmission($s)),
            'meta'    => [
                'current_page' => $submissions->currentPage(),
                'last_page'    => $submissions->lastPage(),
                'per_page'     => $submissions->perPage(),
                'total'        => $submissions->total(),
            ],
        ]);
    }

    /**
     * Detail pengajuan beserta histori persetujuan.
     *
     * GET /api/submissions/{id}
     */
    public function show(Request $request, Submission $submission): JsonResponse
    {
        // Hanya pemilik yang bisa melihat detail
        if ($submission->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pengajuan ini.',
            ], 403);
        }

        $submission->load(['category', 'user', 'approvals.user', 'payment']);

        return response()->json([
            'success' => true,
            'data'    => $this->formatSubmissionDetail($submission),
        ]);
    }

    /**
     * Membuat pengajuan baru.
     *
     * POST /api/submissions
     * Body: { category_id, amount, description, action (draft/submit), attachment[] }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount'      => ['required', 'numeric', 'min:1000'],
            'description' => ['required', 'string', 'max:1000'],
            'action'      => ['required', 'in:draft,submit'],
            'attachment'   => ['nullable', 'array', 'max:5'],
            'attachment.*' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:5120'],
        ]);

        try {
            $submission = $this->service->createSubmission(
                $validated,
                $request->file('attachment'),
                $request->user()->id
            );

            $submission->load(['category', 'approvals.user']);

            return response()->json([
                'success' => true,
                'message' => $validated['action'] === 'submit'
                    ? 'Pengajuan berhasil disubmit.'
                    : 'Pengajuan disimpan sebagai Draft.',
                'data'    => $this->formatSubmission($submission),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengajuan: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Daftar semua kategori yang tersedia.
     *
     * GET /api/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    // ─── Helper Format ────────────────────────────────────────────────

    private function formatSubmission(Submission $s): array
    {
        return [
            'id'                => $s->id,
            'submission_number' => $s->submission_number,
            'submission_date'   => $s->submission_date?->format('Y-m-d'),
            'category'          => $s->category?->name,
            'category_id'       => $s->category_id,
            'amount'            => (float) $s->amount,
            'amount_formatted'  => 'Rp ' . number_format($s->amount, 0, ',', '.'),
            'description'       => $s->description,
            'status'            => $s->status,
            'status_label'      => $s->statusLabel,
            'status_badge'      => $s->statusBadge,
            'has_attachment'    => !empty($s->attachment),
            'created_at'        => $s->created_at?->toISOString(),
        ];
    }

    private function formatSubmissionDetail(Submission $s): array
    {
        $data = $this->formatSubmission($s);

        $data['user'] = [
            'id'    => $s->user?->id,
            'name'  => $s->user?->name,
            'email' => $s->user?->email,
        ];

        $data['attachments'] = collect($s->attachment ?? [])->map(fn ($path, $index) => [
            'index'    => $index,
            'filename' => basename($path),
            'url'      => Storage::disk('public')->exists($path) ? asset('storage/' . $path) : null,
        ])->values()->toArray();

        $data['approvals'] = $s->approvals->map(fn ($a) => [
            'id'          => $a->id,
            'role'        => $a->role,
            'status'      => $a->status,
            'notes'       => $a->notes,
            'approved_at' => $a->approved_at?->toISOString(),
            'user'        => [
                'id'   => $a->user?->id,
                'name' => $a->user?->name,
            ],
        ])->toArray();

        $data['payment'] = $s->payment ? [
            'amount'           => (float) $s->payment->amount,
            'payment_date'     => $s->payment->payment_date?->format('Y-m-d'),
            'payment_method'   => $s->payment->payment_method,
            'reference_number' => $s->payment->reference_number,
        ] : null;

        return $data;
    }
}
