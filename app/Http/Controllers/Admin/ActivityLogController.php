<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Payment;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->with('causer');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhereJsonContains('properties->attributes->name', $search);
            });
        }

        if ($from = $request->query('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->query('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        foreach ($activities as $activity) {
            $activity->display_title = $this->getActivityTitle($activity);
            $activity->display_description = $this->getActivityDescription($activity);
            $activity->causer_role_label = $this->getRoleLabel($activity->causer?->role?->slug ?? null);
            $activity->causer_role_class = $this->getRoleBadgeClass($activity->causer?->role?->slug ?? null);
        }

        return view('admin.activitylogs.index', compact('activities'));
    }

    public function show(Activity $activity)
    {
        $activity->display_title = $this->getActivityTitle($activity);
        $activity->display_description = $this->getActivityDescription($activity);
        $activity->causer_role_label = $this->getRoleLabel($activity->causer?->role?->slug ?? null);
        $activity->causer_role_class = $this->getRoleBadgeClass($activity->causer?->role?->slug ?? null);

        return view('admin.activitylogs.show', compact('activity'));
    }

    protected function getActivityTitle(Activity $activity): string
    {
        $map = [
            'User logged in' => 'Login',
            'User logged out' => 'Logout',
            'Created user' => 'Create',
            'Updated user' => 'Update',
            'Deleted user' => 'Delete',
            'created' => 'Create',
            'updated' => 'Update',
            'deleted' => 'Delete',
            'upload_payment_proof' => 'Upload Payment Proof',
        ];

        if (isset($map[$activity->description])) {
            return $map[$activity->description];
        }

        if (Str::contains($activity->description, 'Approved by') || Str::contains($activity->description, 'Rejected by')) {
            return Str::startsWith($activity->description, 'Approved') ? 'Approve' : 'Reject';
        }

        return Str::title(str_replace('_', ' ', $activity->description));
    }

    protected function getActivityDescription(Activity $activity): string
    {
        $action = $this->getActivityTitle($activity);
        $props = $activity->properties->toArray();
        $attributes = data_get($props, 'attributes', []);
        $old = data_get($props, 'old', []);
        $role = data_get($props, 'role');
        $submissionNumber = data_get($attributes, 'submission_number') ?: data_get($props, 'submission_number');
        $amount = data_get($attributes, 'amount') ?: data_get($props, 'amount');
        $status = data_get($attributes, 'status') ?: data_get($props, 'status');
        $notes = data_get($props, 'notes');

        if ($action === 'Login') {
            return 'Login berhasil.';
        }

        if ($action === 'Logout') {
            return 'Logout berhasil.';
        }

        if ($action === 'Create') {
            if ($activity->subject_type === Submission::class && $submissionNumber && $amount) {
                return 'Membuat pengajuan '.$submissionNumber.' sebesar Rp'.number_format($amount, 0, ',', '.').'.';
            }

            if ($activity->subject_type === User::class && $name = data_get($attributes, 'name')) {
                return 'Membuat pengguna '.$name.'.';
            }

            if ($activity->subject_type === Approval::class) {
                return 'Membuat entri persetujuan untuk pengajuan.';
            }

            if ($activity->subject_type === Payment::class) {
                return 'Membuat entri pembayaran.';
            }

            return 'Membuat data baru.';
        }

        if ($action === 'Submit') {
            if ($submissionNumber) {
                return 'Pengajuan '.$submissionNumber.' disubmit.';
            }

            return 'Pengajuan disubmit.';
        }

        if ($action === 'Approve') {
            $roleLabel = $this->getRoleLabel($role);
            if ($role === 'finance') {
                if ($submissionNumber) {
                    return 'Pembayaran pengajuan '.$submissionNumber.' berhasil diproses.';
                }
                return 'Pembayaran berhasil diproses.';
            }
            return $roleLabel.' menyetujui pengajuan.';
        }

        if ($action === 'Reject') {
            return $this->getRoleLabel($role).' menolak pengajuan.';
        }

        if ($action === 'Process Payment') {
            if ($submissionNumber && $amount) {
                return 'Pembayaran pengajuan '.$submissionNumber.' sebesar Rp'.number_format($amount, 0, ',', '.').' diproses.';
            }
            return 'Finance memproses pembayaran.';
        }

        if ($action === 'Update') {
            if ($status) {
                return 'Status pengajuan diubah menjadi "'.$this->getSubmissionStatusLabel($status).'".';
            }

            if ($activity->subject_type === Submission::class && $submissionNumber) {
                return 'Memperbarui detail pengajuan '.$submissionNumber.'.';
            }

            if ($activity->subject_type === User::class && $name = data_get($attributes, 'name')) {
                return 'Memperbarui pengguna '.$name.'.';
            }

            return 'Memperbarui data.';
        }

        if ($action === 'Delete') {
            if ($activity->subject_type === Submission::class && $submissionNumber) {
                return 'Menghapus pengajuan '.$submissionNumber.'.';
            }

            if ($activity->subject_type === User::class && $name = data_get($attributes, 'name')) {
                return 'Menghapus pengguna '.$name.'.';
            }

            return 'Menghapus data.';
        }

        if (! empty($notes)) {
            return $notes;
        }

        return 'Menjalankan tindakan '.$action.'.';
    }

    protected function getRoleLabel(?string $role): string
    {
        return match ($role) {
            'spv' => 'SPV',
            'manager' => 'Manager',
            'direktur' => 'Direktur',
            'finance' => 'Finance',
            default => ucfirst($role ?? 'Pengguna'),
        };
    }

    protected function getRoleBadgeClass(?string $role): string
    {
        return match ($role) {
            'spv' => 'badge bg-info text-dark',
            'manager' => 'badge bg-warning text-dark',
            'direktur' => 'badge bg-primary',
            'finance' => 'badge bg-success',
            'staff' => 'badge bg-secondary',
            default => 'badge bg-dark',
        };
    }

    protected function getSubmissionStatusLabel(string $status): string
    {
        return match ($status) {
            Submission::STATUS_DRAFT => 'Draft',
            Submission::STATUS_SUBMITTED => 'Dissubmitted',
            Submission::STATUS_WAITING_SPV => 'Waiting SPV Approval',
            Submission::STATUS_WAITING_MANAGER => 'Waiting Manager Approval',
            Submission::STATUS_WAITING_DIREKTUR => 'Waiting Director Approval',
            Submission::STATUS_WAITING_FINANCE => 'Waiting Finance Approval',
            Submission::STATUS_PAID => 'Paid',
            Submission::STATUS_REJECTED => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}
