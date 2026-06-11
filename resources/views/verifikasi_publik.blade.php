<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Laporan Resmi P2PTM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 ring-1 ring-black/5">

        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-patch-check-fill text-4xl"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Dokumen Laporan Terverifikasi</h2>
            <p class="text-slate-500 mt-1 text-xs uppercase tracking-widest font-semibold">Resmi Disahkan oleh Dinas
                Kesehatan</p>
        </div>

        <div class="space-y-4">

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Judul Laporan</p>
                <p class="text-slate-800 font-bold mt-1 text-sm">{{ request('judul') }}</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Periode</p>
                <p class="text-slate-800 font-bold mt-1 text-sm">{{ request('periode') }}</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Tanggal Pengesahan</p>
                <p class="text-slate-800 font-bold mt-1 text-sm">{{ request('tanggal_sah') }} WITA</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Status Pengesahan</p>
                <p class="text-emerald-600 font-bold mt-1 text-sm">
                    <i class="bi bi-check-circle-fill me-1"></i> Disahkan oleh Kepala Bidang P2PTM
                </p>

                {{-- Penambahan Garis Pemisah dan Identitas Pejabat --}}
                <div class="mt-3 pt-3 border-t border-slate-200">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Oleh Pejabat:</p>
                    <p class="text-slate-800 font-bold mt-1 text-sm">{{ request('nama_kepala', 'Deny Haryuniansyah') }}
                    </p>
                    <p class="text-slate-600 text-xs font-mono mt-0.5">NIP. {{ request('nip', '1973062022006041016') }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</body>

</html>