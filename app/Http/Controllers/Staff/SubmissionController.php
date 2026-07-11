<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\Category;
use App\Models\Submission;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use App\Services\SubmissionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionRepositoryInterface $repository,
        protected SubmissionService $service
    ) {}

    public function index(Request $request): View
    {
        $submissions = $this->repository->getForUser($request->user()->id);
        return view('submissions.index', compact('submissions'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('submissions.create', compact('categories'));
    }

    public function store(StoreSubmissionRequest $request): RedirectResponse
    {
        try {
            $this->service->createSubmission(
                $request->validated(),
                $request->file('attachment'),
                $request->user()->id
            );

            $msg = $request->action === 'submit' ? 'Pengajuan berhasil disubmit.' : 'Pengajuan disimpan sebagai Draft.';
            return redirect()->route('submissions.index')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage());
        }
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['category', 'approvals.user']);
        return view('submissions.show', compact('submission'));
    }

    public function edit(Submission $submission): View
    {
        $this->authorize('update', $submission);

        $categories = Category::all();
        return view('submissions.edit', compact('submission', 'categories'));
    }

    public function update(UpdateSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        $this->authorize('update', $submission);

        try {
            $this->service->updateSubmission(
                $submission,
                $request->validated(),
                $request->file('attachment')
            );

            $msg = $request->action === 'submit' ? 'Pengajuan berhasil disubmit.' : 'Pengajuan berhasil diupdate.';
            return redirect()->route('submissions.index')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengupdate pengajuan: ' . $e->getMessage());
        }
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        $this->authorize('delete', $submission);

        try {
            $this->service->deleteSubmission($submission);
            return redirect()->route('submissions.index')->with('success', 'Pengajuan berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadAttachment(Submission $submission, $index)
    {
        $this->authorize('view', $submission);

        $attachments = $submission->attachment ?? [];

        if (!isset($attachments[$index]) || !Storage::disk('public')->exists($attachments[$index])) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->download($attachments[$index]);
    }

    public function viewAttachment(Submission $submission, $index)
    {
        $this->authorize('view', $submission);

        $attachments = $submission->attachment ?? [];

        if (!isset($attachments[$index]) || !Storage::disk('public')->exists($attachments[$index])) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->response($attachments[$index]);
    }
}
