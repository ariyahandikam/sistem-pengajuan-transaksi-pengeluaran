<section>
    <p class="text-muted small mb-4">
        Setelah akun Anda dihapus, semua sumber daya dan data yang dimilikinya akan dihapus secara permanen. Sebelum menghapus akun, pastikan Anda telah menyimpan data atau informasi apa pun yang ingin Anda simpan.
    </p>

    <button type="button" class="btn btn-danger px-4 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        Hapus Akun
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true" x-data="{ open: false }">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">Apakah Anda yakin ingin menghapus akun Anda?</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">
                            Setelah akun Anda dihapus, semua sumber daya dan data yang dimilikinya akan dihapus secara permanen. Masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label visually-hidden">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Password Anda" required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
