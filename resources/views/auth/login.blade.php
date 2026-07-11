<x-guest-layout>
    <div class="text-center mb-5">
        <h3 class="fw-bold text-dark mb-1">Selamat Datang 👋</h3>
        <p class="text-muted">Silakan login untuk mengakses sistem</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 alert alert-info border-0 rounded-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label fw-semibold text-dark small text-uppercase ls-wide">
                <i class="bi bi-envelope-fill me-1 text-primary"></i> Email Address
            </label>
            <div class="auth-input-wrapper">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@perusahaan.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label fw-semibold text-dark small text-uppercase ls-wide mb-0">
                    <i class="bi bi-shield-lock-fill me-1 text-primary"></i> Password
                </label>
                @if (Route::has('password.request'))
                    <a class="text-decoration-none small text-primary fw-semibold" href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>
            <div class="auth-input-wrapper mt-2">
                <i class="bi bi-lock-fill auth-input-icon"></i>
                <input id="password" type="password" class="form-control auth-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukkan password anda">
                <button type="button" class="auth-toggle-password" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input auth-checkbox" name="remember">
                <label class="form-check-label text-muted small" for="remember_me">
                    Ingat Saya
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mt-5">
            <button type="submit" class="btn btn-primary-custom btn-lg">
                Masuk ke Sistem <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i>
            </button>
        </div>
        
    </form>

    <style>
        .ls-wide { letter-spacing: 0.05em; }

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
            padding: 14px 16px 14px 46px;
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

        .auth-input:focus + .auth-input-icon,
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

        .auth-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 5px !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .auth-checkbox:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .auth-checkbox:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
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
