{{-- MODAL VERIFIKASI (VERSI SUPER SIMPLE) --}}
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="verifyForm" method="POST" action="{{ route('pengguna.verifikasi.process') }}">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white px-4 py-3">
                    <h5 class="modal-title fw-bold">Konfirmasi Verifikasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="modal_id">
                    <input type="hidden" name="type" id="modal_type">
                    <input type="hidden" name="action" id="modal_action">
                    <input type="hidden" name="status" id="modal_status">

                    <p class="mb-3">Apakah Anda yakin ingin <strong id="txt_action"></strong> data ini?</p>

                    <textarea name="note" id="verifyNote" rows="2" class="form-control"
                        placeholder="Tuliskan catatan verifikasi (opsional)..."></textarea>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="verifySubmit" class="btn btn-success rounded-pill px-4">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        var verifyModal = document.getElementById('verifyModal');
        verifyModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var action = button.getAttribute('data-action');

            // Isi Hidden Inputs
            document.getElementById('modal_id').value = button.getAttribute('data-id');
            document.getElementById('modal_type').value = button.getAttribute('data-type');
            document.getElementById('modal_action').value = action;
            document.getElementById('modal_status').value = (action === 'approve') ? 'approved' : 'rejected';

            // Update Teks Konfirmasi
            document.getElementById('txt_action').innerText = (action === 'approve') ? 'menyetujui' : 'menolak';

            // Update Style Tombol
            let btn = document.getElementById('verifySubmit');
            btn.className = (action === 'approve') ? 'btn btn-success rounded-pill px-4' : 'btn btn-danger rounded-pill px-4';
            btn.textContent = (action === 'approve') ? 'Setujui' : 'Tolak';
        });
    </script>
@endpush