# Aturan Pengembangan UI (E-PTM)

1. **Penanganan Pesan Sesi (Alerts):**
   Saat membuat atau memodifikasi file *view* (`.blade.php`), JANGAN menambahkan blok kode untuk menampilkan notifikasi *alert* (seperti `session('success')` atau `session('error')`). Fungsionalitas *alert* sudah ditangani secara global dan otomatis di dalam file `layouts/master.blade.php`. Menambahkan *alert* secara manual di file *view* anak akan menyebabkan notifikasi ganda (*duplicate alerts*).
