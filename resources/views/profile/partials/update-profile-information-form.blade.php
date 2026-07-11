<section>
    <p class="text-muted small mb-4">
        Perbarui informasi profil dan alamat email akun Anda.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="needs-validation" novalidate>
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-person-fill me-1 text-primary"></i> Nama Lengkap
            </label>
            <div class="profile-input-wrapper">
                <i class="bi bi-person profile-input-icon"></i>
                <input id="name" name="name" type="text" class="form-control profile-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Nama lengkap Anda">
            </div>
            @error('name')
                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-envelope-fill me-1 text-primary"></i> Alamat Email
            </label>
            <div class="profile-input-wrapper">
                <i class="bi bi-envelope profile-input-icon"></i>
                <input id="email" name="email" type="email" class="form-control profile-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="nama@perusahaan.com">
            </div>
            @error('email')
                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-warning small mb-1">
                        <i class="bi bi-exclamation-triangle me-1"></i>Email Anda belum diverifikasi.
                    </p>
                    <button form="send-verification" class="btn btn-link p-0 text-decoration-none small">
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small mt-2 mb-0">
                            <i class="bi bi-check-circle me-1"></i>Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-success small fw-medium">
                    <i class="bi bi-check-circle me-1"></i>Berhasil disimpan.
                </span>
            @endif
        </div>
    </form>

    <style>
        .profile-input-wrapper {
            position: relative;
        }

        .profile-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            z-index: 2;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .profile-input {
            padding: 12px 16px 12px 42px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 0.9rem;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: #1e293b;
        }

        .profile-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 12px rgba(59, 130, 246, 0.08);
        }

        .profile-input-wrapper:focus-within .profile-input-icon {
            color: #3b82f6;
        }

        .profile-input::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }
    </style>
</section>
