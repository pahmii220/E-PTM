@extends('layouts.master')

@section('title', 'Pusat Bantuan & FAQ')

@section('content')
<div class="container-fluid px-4 py-5" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <div class="text-center mb-5 pb-3">
        <h2 class="fw-bold text-slate-800 mb-3" style="color: #1e293b;">Halo, Ada yang bisa kami bantu?</h2>
        <p class="text-muted fs-5" style="color: #64748b;">Pusat bantuan interaktif untuk Petugas Puskesmas E-PTM</p>
    </div>

    <div class="row g-5 justify-content-center">
        <!-- Main FAQ Accordion -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4" style="background-color: #ffffff;">
                <div class="card-body p-4 p-lg-5">
                    
                    <!-- KATEGORI 1 -->
                    <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex align-items-center" style="color: #334155;">
                        <span class="bg-indigo-100 text-indigo-600 rounded-circle d-inline-flex justify-content-center align-items-center me-3" style="width: 35px; height: 35px; background-color: #e0e7ff; color: #4f46e5;">
                            <i class="bi bi-person-badge"></i>
                        </span>
                        1. Data Pasien & Pendaftaran
                    </h5>
                    <div class="accordion accordion-flush mb-5" id="faqPasien">
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                    Bagaimana cara mendaftarkan pasien baru?
                                </button>
                            </h2>
                            <div id="q1" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Buka menu <strong>Pemeriksaan PTM -> Data Pasien</strong>, lalu klik tombol biru "Tambah Pasien Baru". Pastikan Anda mengisi NIK yang valid (16 digit) karena NIK tidak boleh ganda di dalam sistem.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                    Mengapa NIK pasien muncul pesan *error* "sudah terdaftar"?
                                </button>
                            </h2>
                            <div id="q2" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Ini berarti pasien tersebut sudah pernah didaftarkan (mungkin oleh petugas lain di puskesmas Anda). Anda tidak perlu mendaftarkannya lagi. Cukup cari namanya di kolom pencarian tabel pasien, lalu klik ikon "Tambah Deteksi Dini" pada baris pasien tersebut.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KATEGORI 2 -->
                    <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex align-items-center" style="color: #334155;">
                        <span class="bg-indigo-100 text-indigo-600 rounded-circle d-inline-flex justify-content-center align-items-center me-3" style="width: 35px; height: 35px; background-color: #dcfce7; color: #16a34a;">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </span>
                        2. Pemeriksaan Medis
                    </h5>
                    <div class="accordion accordion-flush mb-5" id="faqMedis">
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                    Bagaimana format penulisan Tekanan Darah?
                                </button>
                            </h2>
                            <div id="q3" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Tekanan darah harus ditulis dengan format <strong>Sistolik/Diastolik</strong> tanpa spasi. Contoh yang benar: <code>120/80</code>. Jika Anda mengetik dengan format yang salah, sistem tidak akan bisa menyimpannya.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q4">
                                    Kenapa Berat dan Tinggi Badan wajib diisi?
                                </button>
                            </h2>
                            <div id="q4" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Sistem kami dilengkapi dengan kalkulator IMT (Indeks Massa Tubuh) otomatis. Dengan mengisi BB dan TB, sistem akan langsung mendeteksi apakah pasien mengalami <strong>Obesitas</strong> atau Normal tanpa perlu Anda hitung manual.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q5">
                                    Kapan saya harus mengisi form Tindak Lanjut PTM?
                                </button>
                            </h2>
                            <div id="q5" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Tindak lanjut diisi hanya jika pasien didiagnosa memiliki penyakit (seperti Hipertensi atau Diabetes) dari hasil skrining awal. Jika pasien sehat (Normal), form tindak lanjut bersifat opsional.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KATEGORI 3 -->
                    <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex align-items-center" style="color: #334155;">
                        <span class="bg-indigo-100 text-indigo-600 rounded-circle d-inline-flex justify-content-center align-items-center me-3" style="width: 35px; height: 35px; background-color: #fee2e2; color: #dc2626;">
                            <i class="bi bi-file-earmark-check"></i>
                        </span>
                        3. Pengiriman Laporan (Otomatis)
                    </h5>
                    <div class="accordion accordion-flush mb-5" id="faqLaporan">
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q6">
                                    Bagaimana alur pengiriman laporan ke Dinkes?
                                </button>
                            </h2>
                            <div id="q6" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Anda cukup menginput data pemeriksaan pasien seperti biasa setiap harinya. Setelah itu, buka menu <strong>Laporan & Pengajuan</strong>, pastikan seluruh datanya sudah benar, lalu klik tombol <strong>Kirim Laporan</strong>. Data akan otomatis masuk ke sistem pemantauan Dinkes.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q7">
                                    Apakah masih ada proses Verifikasi oleh Pegawai Dinkes?
                                </button>
                            </h2>
                            <div id="q7" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    <strong>Tidak ada lagi.</strong> Sesuai pembaruan sistem terbaru, tahapan verifikasi manual ("Menunggu", "Disetujui", "Ditolak") oleh Pegawai Dinkes telah ditiadakan. Semua laporan yang Anda kirim akan otomatis dianggap sah (auto-approve) dan terekap ke dalam laporan resmi Kepala P2PTM.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q8">
                                    Kapan batas waktu (deadline) pengiriman laporan bulanan?
                                </button>
                            </h2>
                            <div id="q8" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Sangat dianjurkan untuk menyelesaikan proses input data dan menekan tombol Kirim Laporan paling lambat pada <strong>tanggal 5 di bulan berikutnya</strong>. Hal ini agar laporan Puskesmas Anda masuk tepat waktu ke dalam rekapitulasi data kota.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KATEGORI 4 -->
                    <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex align-items-center" style="color: #334155;">
                        <span class="bg-indigo-100 text-indigo-600 rounded-circle d-inline-flex justify-content-center align-items-center me-3" style="width: 35px; height: 35px; background-color: #fef08a; color: #ca8a04;">
                            <i class="bi bi-gear"></i>
                        </span>
                        4. Akun & Keamanan
                    </h5>
                    <div class="accordion accordion-flush mb-2" id="faqAkun">
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q9">
                                    Bagaimana cara mengubah profil dan password?
                                </button>
                            </h2>
                            <div id="q9" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Klik nama Anda di pojok kanan atas layar, lalu pilih menu <strong>Pengaturan Akun</strong>. Anda bisa mengubah Foto Profil, Username, dan Password lama Anda di sana demi menjaga keamanan data puskesmas.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-slate-700 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#q10">
                                    Bagaimana jika saya lupa password akun saya?
                                </button>
                            </h2>
                            <div id="q10" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted pt-0 pb-4" style="font-size: 0.95rem;">
                                    Karena sistem E-PTM memegang data medis rahasia, Anda <strong>tidak bisa</strong> mengganti password sembarangan di halaman login. Silakan hubungi langsung Pegawai Dinkes / Administrator melalui WhatsApp untuk mereset akun Anda secara manual.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Sidebar Bantuan -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Contact Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
                    <div class="card-body p-4 text-center text-white relative">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mt-2" style="width:70px; height:70px; backdrop-filter: blur(5px);">
                            <i class="bi bi-headset fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Pusat Layanan IT</h5>
                        <p class="text-white-50 small mb-4" style="line-height: 1.6;">Masih kebingungan? Hubungi tim administrator teknis kami untuk mendapatkan panduan langsung via Email.</p>
                        
                        @if(session('success'))
                            <div class="alert alert-success bg-white text-success border-0 py-2 small fw-bold mb-3">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                            </div>
                        @endif

                        <button type="button" class="btn btn-light text-primary rounded-pill fw-bold w-100 py-2 shadow-sm transition-all hover-scale" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-envelope-fill me-2 text-primary"></i> Kirim Pesan ke Admin
                        </button>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card shadow-sm border-0 rounded-4" style="background-color: #ffffff;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold d-flex align-items-center mb-4" style="color: #334155;">
                            <span class="bg-warning-subtle text-warning rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 28px; height: 28px;">
                                <i class="bi bi-lightbulb-fill"></i>
                            </span>
                            Pintasan Cepat
                        </h6>
                        
                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-6"></i>
                            <div>
                                <p class="mb-0 fw-semibold text-slate-700" style="font-size: 0.9rem;">Periksa Lonceng Notifikasi</p>
                                <p class="text-muted small mb-0 mt-1">Dinkes mengirim pesan peringatan melalui ikon lonceng di pojok kanan atas.</p>
                            </div>
                        </div>
                        <hr class="border-light opacity-50">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-6"></i>
                            <div>
                                <p class="mb-0 fw-semibold text-slate-700" style="font-size: 0.9rem;">Data Tidak Bisa Diubah?</p>
                                <p class="text-muted small mb-0 mt-1">Data yang sudah berstatus <strong class="text-success">Disetujui</strong> tidak dapat dihapus/diedit karena telah dikunci oleh Dinkes.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kontak Email -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="contactModalLabel" style="color: #334155;">
                    <i class="bi bi-envelope-paper-fill text-primary me-2"></i>Kirim Pesan Bantuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('petugas.faq.contact') }}" method="POST">
                @csrf
                <div class="modal-body pt-3 pb-2">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Subjek Kendala <span class="text-danger">*</span></label>
                        <select class="form-select" name="subjek" required>
                            <option value="" disabled selected>Pilih jenis kendala...</option>
                            <option value="Kendala Login/Akun">Kendala Login / Akun</option>
                            <option value="Error Input Data">Error Input Data Pasien</option>
                            <option value="Pertanyaan Verifikasi">Pertanyaan seputar Verifikasi</option>
                            <option value="Saran/Lainnya">Saran / Kendala Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Pesan Anda <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="pesan" rows="4" placeholder="Jelaskan kendala Anda secara detail di sini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-send-fill me-2"></i>Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Styling Accordion yang Clean & Soft */
    .accordion-button {
        color: #334155 !important; /* slate-700 */
        font-size: 1rem;
        padding: 1.25rem 0;
        transition: all 0.3s ease;
    }
    .accordion-button:not(.collapsed) {
        color: #4f46e5 !important; /* indigo-600 */
        background-color: transparent !important;
        box-shadow: none !important;
        border-bottom: none;
    }
    .accordion-button:focus {
        border-color: rgba(0,0,0,.125);
        box-shadow: none !important;
    }
    .accordion-button::after {
        transition: transform 0.3s ease-in-out;
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.03);
    }
    .text-slate-700 { color: #334155; }
    .text-slate-800 { color: #1e293b; }
    
    /* Fix Tailwind vs Bootstrap .collapse conflict */
    .accordion-collapse.collapse.show {
        visibility: visible !important;
    }
    .accordion-collapse {
        visibility: visible !important;
    }
</style>
@endpush
