<x-guest-layout>
    <div class="text-center mb-4">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
            <i class="bi bi-shield-lock-fill text-primary fs-4"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Reset Password</h3>
        <p class="text-muted small px-2">
            Buat password baru untuk mengamankan akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label fw-semibold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-envelope-fill me-1 text-primary"></i> Email
            </label>
            <div class="auth-input-wrapper">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="nama@perusahaan.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label fw-semibold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-lock-fill me-1 text-primary"></i> Password Baru
            </label>
            <div class="auth-input-wrapper">
                <i class="bi bi-lock-fill auth-input-icon"></i>
                <input id="password" type="password" class="form-control auth-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                <button type="button" class="auth-toggle-password" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-lock-fill me-1 text-primary"></i> Konfirmasi Password
            </label>
            <div class="auth-input-wrapper">
                <i class="bi bi-lock-fill auth-input-icon"></i>
                <input id="password_confirmation" type="password" class="form-control auth-input @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru">
                <button type="button" class="auth-toggle-password" onclick="togglePassword('password_confirmation', this)">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary-custom btn-lg">
                <i class="bi bi-check-circle-fill me-2"></i> Simpan Password Baru
            </button>
        </div>
    </form>

    <style>
        .auth-input-wrapper {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 2;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .auth-input {
            padding: 14px 50px 14px 46px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            font-size: 0.95rem;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: #1e293b;
        }

        .auth-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 12px rgba(59, 130, 246, 0.08);
        }

        .auth-input-wrapper:focus-within .auth-input-icon {
            color: #3b82f6;
        }

        .auth-input::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }

        .auth-toggle-password {
            position: absolute;
            right: 14px;
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

        .auth-toggle-password:hover {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.08);
        }
    </style>

    <script>
        function togglePassword(fieldId, btn) {
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
</x-guest-layout>
