<section>
    <p class="text-muted small mb-4">
        Pastikan akun Anda menggunakan password yang panjang dan acak untuk menjaga keamanan.
    </p>

    <form method="post" action="{{ route('password.update') }}" class="needs-validation" novalidate>
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-lock-fill me-1 text-warning"></i> Password Saat Ini
            </label>
            <div class="profile-input-wrapper">
                <i class="bi bi-lock profile-input-icon"></i>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control profile-input @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" placeholder="Masukkan password saat ini">
                <button type="button" class="profile-toggle-password" onclick="toggleProfilePassword('update_password_current_password', this)">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Password Baru
            </label>
            <div class="profile-input-wrapper">
                <i class="bi bi-shield-lock profile-input-icon"></i>
                <input id="update_password_password" name="password" type="password" class="form-control profile-input @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="Minimal 8 karakter">
                <button type="button" class="profile-toggle-password" onclick="toggleProfilePassword('update_password_password', this)">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            @error('password', 'updatePassword')
                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-shield-check me-1 text-warning"></i> Konfirmasi Password Baru
            </label>
            <div class="profile-input-wrapper">
                <i class="bi bi-shield-check profile-input-icon"></i>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control profile-input @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="Ulangi password baru">
                <button type="button" class="profile-toggle-password" onclick="toggleProfilePassword('update_password_password_confirmation', this)">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-warning px-4 shadow-sm rounded-pill fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Ubah Password
            </button>

            @if (session('status') === 'password-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-success small fw-medium">
                    <i class="bi bi-check-circle me-1"></i>Berhasil diubah.
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
            padding: 12px 48px 12px 42px;
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

        .profile-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: all 0.2s ease;
            z-index: 2;
        }

        .profile-toggle-password:hover {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.08);
        }
    </style>

    <script>
        function toggleProfilePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }
    </script>
</section>
