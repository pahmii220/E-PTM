<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tidak Valid</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 text-center ring-1 ring-black/5">
        <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-exclamation-triangle text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800">Data Tidak Ditemukan</h2>
        <p class="text-slate-500 mt-2 text-sm">{{ $pesan }}</p>

        <div class="mt-8 border-t pt-6">
            <p class="text-[10px] text-slate-400 uppercase font-bold">Sistem Informasi P2PTM</p>
        </div>
    </div>

</body>

</html>