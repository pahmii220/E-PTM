<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="verifyForm" method="POST" action="{{ route('pengguna.verifikasi.process') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="modal_id">
                    <input type="hidden" name="type" id="modal_type">
                    <input type="hidden" name="action" id="verifyAction">

                    <div class="mb-3">
                        <label for="verifyNote" class="form-label">Catatan (opsional)</label>
                        <textarea name="note" id="verifyNote" rows="4" class="form-control"></textarea>
                    </div>
                    <p class="text-muted">Anda sedang melakukan verifikasi. Pastikan data sudah diperiksa.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="verifySubmit" class="btn btn-primary">Kirim</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var verifyModal = document.getElementById('verifyModal');
            verifyModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                // Ambil data dari tombol
                var id = button.getAttribute('data-id');
                var type = button.getAttribute('data-type');
                var action = button.getAttribute('data-action');

                // 3. Masukkan ke input hidden agar terkirim ke Controller
                document.getElementById('modal_id').value = id;
                document.getElementById('modal_type').value = type;
                document.getElementById('verifyAction').value = action;

                // Set tampilan catatan dan tombol
                document.getElementById('verifyNote').value = (action === 'approve') ? 'Disetujui oleh {{ auth()->user()->Nama_Lengkap ?? 'Admin' }}' : '';
                document.getElementById('verifySubmit').textContent = (action === 'approve') ? 'Approve' : 'Reject';

                if (action === 'approve') {
                    document.getElementById('verifySubmit').classList.remove('btn-danger');
                    document.getElementById('verifySubmit').classList.add('btn-success');
                } else {
                    document.getElementById('verifySubmit').classList.remove('btn-success');
                    document.getElementById('verifySubmit').classList.add('btn-danger');
                }
            });
        });
    </script>
@endpush