<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditTrailController extends Controller
{
    /**
     * Daftar modul yang bisa difilter.
     */
    private const MODULES = [
        'Submissions'  => 'Pengajuan',
        'Approvals'    => 'Persetujuan',
        'Payments'     => 'Pembayaran',
        'Users'        => 'Pengguna',
        'Budgets'      => 'Anggaran',
        'Auth'         => 'Autentikasi',
    ];

    /**
     * Daftar aksi yang bisa difilter.
     */
    private const ACTIONS = [
        'created'  => 'Buat / Tambah',
        'updated'  => 'Ubah / Edit',
        'deleted'  => 'Hapus',
        'approved' => 'Setujui',
        'rejected' => 'Tolak',
        'login'    => 'Login',
        'logout'   => 'Logout',
    ];

    /**
     * Halaman utama Audit Trail.
     */
    public function index(Request $request)
    {
        $query = Activity::query()->with('causer.role');

        // --- FILTER: Pencarian teks bebas ---
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%")
                  ->orWhereHas('causer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        // --- FILTER: Modul ---
        if ($module = $request->query('module')) {
            $query->where(function ($q) use ($module) {
                $q->whereJsonContains('properties->module', $module)
                  ->orWhere('log_name', Str::lower($module));
            });
        }

        // --- FILTER: Aksi ---
        if ($action = $request->query('action')) {
            if (in_array($action, ['login', 'logout'])) {
                $query->where('description', 'like', "%{$action}%");
            } else {
                $query->where('event', $action);
            }
        }

        // --- FILTER: User ---
        if ($userId = $request->query('user_id')) {
            $query->where('causer_id', $userId);
        }

        // --- FILTER: Rentang Tanggal ---
        if ($from = $request->query('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->query('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $audits = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Enrich each record for display
        foreach ($audits as $audit) {
            $audit->display_module  = $this->resolveModule($audit);
            $audit->display_action  = $this->resolveAction($audit);
            $audit->display_detail  = $this->resolveDetail($audit);
            $audit->action_color    = $this->resolveActionColor($audit);
            $audit->causer_role     = $audit->causer?->role?->slug ?? null;
        }

        // Data untuk dropdown filter
        $users   = User::orderBy('name')->get(['id', 'name']);
        $modules = self::MODULES;
        $actions = self::ACTIONS;

        // Statistik ringkasan
        $stats = $this->getStats($request);

        return view('admin.audit-trail.index', compact(
            'audits', 'users', 'modules', 'actions', 'stats'
        ));
    }

    /**
     * Halaman detail Audit Trail.
     */
    public function show(Activity $activity)
    {
        $activity->load('causer.role');

        $activity->display_module = $this->resolveModule($activity);
        $activity->display_action = $this->resolveAction($activity);
        $activity->display_detail = $this->resolveDetail($activity);
        $activity->action_color   = $this->resolveActionColor($activity);

        // Parse changes (old vs new)
        $changes = $this->parseChanges($activity);

        return view('admin.audit-trail.show', compact('activity', 'changes'));
    }

    /**
     * Export Audit Trail ke CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Activity::query()->with('causer.role');

        if ($from = $request->query('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->query('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }
        if ($module = $request->query('module')) {
            $query->where(function ($q) use ($module) {
                $q->whereJsonContains('properties->module', $module)
                  ->orWhere('log_name', Str::lower($module));
            });
        }
        if ($userId = $request->query('user_id')) {
            $query->where('causer_id', $userId);
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_trail_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($activities) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Waktu', 'Pengguna', 'Role', 'Modul', 'Aksi', 'Detail', 'IP Address']);

            foreach ($activities as $act) {
                fputcsv($handle, [
                    $act->created_at->format('d/m/Y H:i:s'),
                    $act->causer?->name ?? 'System',
                    $act->causer?->role?->name ?? '-',
                    $this->resolveModule($act),
                    $this->resolveAction($act),
                    $this->resolveDetail($act),
                    data_get($act->properties, 'ip', '-'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─── Helper Methods ──────────────────────────────────────────────

    /**
     * Mendapatkan statistik ringkasan.
     */
    private function getStats(Request $request): array
    {
        $baseQuery = Activity::query();

        if ($from = $request->query('from')) {
            $baseQuery->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->query('to')) {
            $baseQuery->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        // Jika tidak ada filter tanggal, default ke 30 hari terakhir
        if (!$request->query('from') && !$request->query('to')) {
            $baseQuery->where('created_at', '>=', now()->subDays(30));
        }

        return [
            'total'    => (clone $baseQuery)->count(),
            'creates'  => (clone $baseQuery)->where('event', 'created')->count(),
            'updates'  => (clone $baseQuery)->where('event', 'updated')->count(),
            'deletes'  => (clone $baseQuery)->where('event', 'deleted')->count(),
        ];
    }

    /**
     * Resolve nama modul yang ramah ditampilkan.
     */
    private function resolveModule(Activity $activity): string
    {
        $module = data_get($activity->properties, 'module');
        if ($module && isset(self::MODULES[$module])) {
            return self::MODULES[$module];
        }

        if ($activity->subject_type) {
            $basename = class_basename($activity->subject_type);
            return match ($basename) {
                'Submission' => 'Pengajuan',
                'Approval'  => 'Persetujuan',
                'Payment'   => 'Pembayaran',
                'User'      => 'Pengguna',
                'Budget'    => 'Anggaran',
                default     => $basename,
            };
        }

        return match ($activity->log_name) {
            'auth'       => 'Autentikasi',
            'submission' => 'Pengajuan',
            'approval'   => 'Persetujuan',
            'payment'    => 'Pembayaran',
            'budget'     => 'Anggaran',
            default      => ucfirst($activity->log_name ?? 'Sistem'),
        };
    }

    /**
     * Resolve aksi yang ramah ditampilkan.
     */
    private function resolveAction(Activity $activity): string
    {
        $desc = $activity->description;

        if (Str::contains($desc, 'logged in') || Str::contains($desc, 'Login'))   return 'Login';
        if (Str::contains($desc, 'logged out') || Str::contains($desc, 'Logout')) return 'Logout';
        if (Str::contains($desc, 'Approved'))   return 'Disetujui';
        if (Str::contains($desc, 'Rejected'))   return 'Ditolak';

        return match ($activity->event) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            default   => Str::title(str_replace('_', ' ', $desc)),
        };
    }

    /**
     * Resolve warna badge untuk aksi.
     */
    private function resolveActionColor(Activity $activity): string
    {
        $action = $this->resolveAction($activity);

        return match (true) {
            in_array($action, ['Dibuat', 'Login'])       => 'success',
            in_array($action, ['Diubah'])                => 'warning',
            in_array($action, ['Dihapus', 'Ditolak'])    => 'danger',
            in_array($action, ['Disetujui'])             => 'primary',
            in_array($action, ['Logout'])                => 'secondary',
            default                                      => 'info',
        };
    }

    /**
     * Resolve detail deskriptif aktivitas.
     */
    private function resolveDetail(Activity $activity): string
    {
        $props      = $activity->properties->toArray();
        $attributes = data_get($props, 'attributes', []);
        $old        = data_get($props, 'old', []);

        // Submission
        $submissionNumber = data_get($attributes, 'submission_number');
        $amount           = data_get($attributes, 'amount');
        $status           = data_get($attributes, 'status');

        if ($submissionNumber && $amount) {
            return "Pengajuan {$submissionNumber} — Rp " . number_format((float) $amount, 0, ',', '.');
        }

        if ($status && $old) {
            $oldStatus = data_get($old, 'status');
            if ($oldStatus) {
                return "Status berubah: {$this->statusLabel($oldStatus)} → {$this->statusLabel($status)}";
            }
            return "Status: {$this->statusLabel($status)}";
        }

        // User
        if ($name = data_get($attributes, 'name')) {
            $email = data_get($attributes, 'email', '');
            return "Pengguna: {$name}" . ($email ? " ({$email})" : '');
        }

        // Approval
        $role  = data_get($props, 'role') ?? data_get($attributes, 'role');
        $notes = data_get($props, 'notes') ?? data_get($attributes, 'notes');
        if ($role) {
            $roleName = $this->roleLabel($role);
            return $notes ? "{$roleName}: {$notes}" : "Oleh: {$roleName}";
        }

        // Auth
        if (Str::contains($activity->description, 'logged in'))  return 'Login berhasil.';
        if (Str::contains($activity->description, 'logged out')) return 'Logout berhasil.';

        return Str::limit($activity->description, 80);
    }

    /**
     * Parse perubahan data (sebelum & sesudah) untuk tampilan diff.
     */
    private function parseChanges(Activity $activity): array
    {
        $props      = $activity->properties->toArray();
        $attributes = data_get($props, 'attributes', []);
        $old        = data_get($props, 'old', []);

        $changes = [];

        // Gabungkan semua key yang berubah
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($attributes)));

        foreach ($allKeys as $key) {
            // Skip metadata keys
            if (in_array($key, ['module', 'ip', 'user_agent'])) continue;

            $oldVal = $old[$key] ?? null;
            $newVal = $attributes[$key] ?? null;

            // Format nilai agar lebih readable
            $oldDisplay = $this->formatValue($key, $oldVal);
            $newDisplay = $this->formatValue($key, $newVal);

            $changes[] = [
                'field'    => $this->fieldLabel($key),
                'old'      => $oldDisplay,
                'new'      => $newDisplay,
                'changed'  => $oldVal !== $newVal,
            ];
        }

        return $changes;
    }

    /**
     * Label field yang ramah.
     */
    private function fieldLabel(string $key): string
    {
        return match ($key) {
            'submission_number' => 'No. Pengajuan',
            'submission_id'     => 'ID Pengajuan',
            'user_id'           => 'ID Pengguna',
            'category_id'       => 'ID Kategori',
            'amount'            => 'Nominal',
            'status'            => 'Status',
            'name'              => 'Nama',
            'email'             => 'Email',
            'role'              => 'Role',
            'role_id'           => 'ID Role',
            'notes'             => 'Catatan',
            'payment_method'    => 'Metode Pembayaran',
            'reference_number'  => 'No. Referensi',
            'total_budget'      => 'Total Anggaran',
            'used_budget'       => 'Anggaran Terpakai',
            'description'       => 'Keterangan',
            'approved_at'       => 'Waktu Persetujuan',
            'payment_date'      => 'Tanggal Pembayaran',
            default             => Str::title(str_replace('_', ' ', $key)),
        };
    }

    /**
     * Format nilai agar lebih mudah dibaca.
     */
    private function formatValue(string $key, $value): string
    {
        if (is_null($value)) return '-';
        if (is_array($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);

        if ($key === 'amount' || $key === 'total_budget' || $key === 'used_budget') {
            return 'Rp ' . number_format((float) $value, 0, ',', '.');
        }

        if ($key === 'status') {
            return $this->statusLabel($value);
        }

        return (string) $value;
    }

    /**
     * Label status pengajuan.
     */
    private function statusLabel(string $status): string
    {
        return match ($status) {
            'draft'             => 'Draft',
            'submitted'         => 'Disubmit',
            'waiting_spv'       => 'Menunggu SPV',
            'waiting_manager'   => 'Menunggu Manager',
            'waiting_direktur'  => 'Menunggu Direktur',
            'waiting_finance'   => 'Menunggu Finance',
            'paid'              => 'Dibayar',
            'rejected'          => 'Ditolak',
            'approved'          => 'Disetujui',
            default             => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Label role.
     */
    private function roleLabel(string $role): string
    {
        return match ($role) {
            'spv'      => 'SPV',
            'manager'  => 'Manager',
            'direktur' => 'Direktur',
            'finance'  => 'Finance',
            'staff'    => 'Staff',
            'admin'    => 'Admin',
            default    => ucfirst($role),
        };
    }
}
