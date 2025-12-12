<!-- Modal Verifikasi -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="verifyForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="verifyAction" value="">
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
                var id = button.getAttribute('data-id');
                var type = button.getAttribute('data-type');
                var action = button.getAttribute('data-action');

                // set form action depending on type
                var form = document.getElementById('verifyForm');
                var route = '';
                if (type === 'pasien') {
                    route = "{{ url('/pengguna/verifikasi/pasien') }}/" + id;
                } else if (type === 'deteksi') {
                    route = "{{ url('/pengguna/verifikasi/deteksi') }}/" + id;
                } else if (type === 'faktor') {
                    route = "{{ url('/pengguna/verifikasi/faktor') }}/" + id;
                }
                form.action = route;

                // set action and reset note
                document.getElementById('verifyAction').value = action;
                document.getElementById('verifyNote').value = (action === 'approve') ? 'Disetujui oleh {{ auth()->user()->Nama_Lengkap }}' : '';
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