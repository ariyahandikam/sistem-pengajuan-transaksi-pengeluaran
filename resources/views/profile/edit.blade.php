<x-app-layout>
    <x-slot name="header">Profil Pengguna</x-slot>

    <div class="row g-4 fade-in-up">
        <!-- Update Profile Info -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>Informasi Profil
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <!-- Update Password -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
