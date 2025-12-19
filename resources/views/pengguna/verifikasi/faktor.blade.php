@extends('layouts.master')

@section('title', 'Verifikasi - Faktor Risiko')

@section('content')
    <div class="container py-3">

        <!-- HEADER: Judul di Kiri, Tools (Cetak & Filter) di Kanan -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Verifikasi - Faktor Risiko</h4>
            <br>
            <br>
            <div class="d-flex gap-2 align-items-center">
                {{-- Tombol Cetak --}}
                <a href="{{ route('pengguna.verifikasi.print.faktor', ['status' => $status ?? 'pending']) }}"
                    class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-printer"></i> Cetak Data
                </a>

                {{-- Filter --}}
                <form method="GET" class="d-flex ms-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="min-width: 120px;">
                        <option value="pending" {{ ($status ?? 'pending') == 'pending' ? 'selected' : '' }}>Tertunda</option>
                        <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="all" {{ ($status ?? '') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Peserta</th>
                                <th>Tanggal</th>
                                <th>Merokok</th>
                                <th>Alkohol</th>
                                <th>Puskesmas</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td class="ps-3">{{ $loop->iteration + (($data->currentPage() - 1) * $data->perPage()) }}</td>

                                    <td class="fw-semibold">{{ optional($row->pasien)->nama_lengkap ?? '-' }}</td>

                                    <td>{{ $row->tanggal_pemeriksaan?->format('d-m-Y') ?? '-' }}</td>

                                    <td class="text-center">{{ $row->merokok ?? '-' }}</td>

                                    <td class="text-center">{{ $row->alkohol ?? '-' }}</td>

                                    <td>
                                        {{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($row->verification_status == 'approved')
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif($row->verification_status == 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">Tertunda</span>
                                        @endif
                                    </td>

                                    <td class="small text-muted">{{ $row->created_at->format('d-m-Y H:i') }}</td>

                                    <td class="text-end pe-3">
                                        @if ($row->verification_status == 'pending')
                                            <div class="d-flex justify-content-end gap-1">
                                                <button class="btn btn-success btn-sm verify-action-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#verifyModal"
                                                    data-id="{{ $row->id }}"
                                                    data-type="faktor"
                                                    data-action="approve"
                                                    title="Diterima">
                                                    <i class="bi bi-check-lg"></i>
                                                    <span class="d-none d-md-inline"> Diterima</span>
                                                </button>

                                                <button class="btn btn-danger btn-sm verify-action-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#verifyModal"
                                                    data-id="{{ $row->id }}"
                                                    data-type="faktor"
                                                    data-action="reject"
                                                    title="Ditolak">
                                                    <i class="bi bi-x-lg"></i>
                                                    <span class="d-none d-md-inline"> Ditolak</span>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        Tidak ada data faktor risiko ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $data->links() }}
        </div>

    </div>

    <!-- Modal Verifikasi (sama dengan deteksi) -->
    <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="verifyForm" method="POST" action="{{ route('pengguna.verifikasi.process') }}">
                @csrf
                <input type="hidden" name="id">
                <input type="hidden" name="type">
                <input type="hidden" name="action">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verifyModalLabel">Konfirmasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <p id="verifyMessage">Apakah Anda yakin ingin melakukan aksi ini?</p>

                        <div id="verifyNoteContainer" class="mt-3 d-none">
                            <label for="verify_note" class="form-label small">Catatan (opsional)</label>
                            <textarea id="verify_note" name="note" class="form-control form-control-sm" rows="2"
                                placeholder="Alasan penolakan atau catatan..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="verifySubmitBtn" class="btn btn-primary btn-sm">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const verifyModalEl = document.getElementById('verifyModal');
            const verifyModal = new bootstrap.Modal(verifyModalEl);
            const verifyForm = document.getElementById('verifyForm');
            const verifyNoteContainer = document.getElementById('verifyNoteContainer');
            const verifyMessage = document.getElementById('verifyMessage');
            const verifySubmitBtn = document.getElementById('verifySubmitBtn');

            verifyModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const type = button.getAttribute('data-type') || 'faktor';
                const action = button.getAttribute('data-action');

                // Set hidden inputs
                verifyForm.querySelector('input[name="id"]').value = id;
                verifyForm.querySelector('input[name="type"]').value = type;
                verifyForm.querySelector('input[name="action"]').value = action;

                // Show/hide note field for reject
                if (action === 'reject') {
                    verifyNoteContainer.classList.remove('d-none');
                    verifyMessage.textContent = 'Anda akan menolak laporan ini. Apakah Anda yakin? (Catatan opsional disarankan)';
                    verifySubmitBtn.classList.remove('btn-primary');
                    verifySubmitBtn.classList.add('btn-danger');
                } else {
                    verifyNoteContainer.classList.add('d-none');
                    verifyMessage.textContent = 'Anda akan menyetujui laporan ini. Apakah Anda yakin?';
                    verifySubmitBtn.classList.remove('btn-danger');
                    verifySubmitBtn.classList.add('btn-primary');
                }
            });

            // Intercept form submit and send via fetch (AJAX)
            verifyForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(verifyForm);
                const submitUrl = verifyForm.getAttribute('action') || window.location.href;

                // disable submit btn
                verifySubmitBtn.disabled = true;
                const originalText = verifySubmitBtn.innerHTML;
                verifySubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';

                fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(res => res.json().catch(() => ({ success: false, message: 'Response bukan JSON' })))
                .then((json) => {
                    if (json.success) {
                        verifyModal.hide();
                        location.reload();
                    } else {
                        alert(json.message || 'Gagal memproses permintaan.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat mengirim permintaan.');
                })
                .finally(() => {
                    verifySubmitBtn.disabled = false;
                    verifySubmitBtn.innerHTML = originalText;
                });
            });

        });
    </script>
@endpush
