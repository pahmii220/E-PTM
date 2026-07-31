@php
    $semuaPuskesmasMaster = $semuaPuskesmasMaster ?? \App\Models\Puskesmas::select('id', 'nama_puskesmas', 'kecamatan', 'nama_kabupaten')->get();
@endphp
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-light filter-jenjang-container">
    <div class="card-body">
        <form action="{{ url()->current() }}" method="GET">
            @if(request('tab'))
                <input type="hidden" name="tab" x-model="activeTab">
            @endif
            <div class="row g-3">
                {{-- KOTA/KABUPATEN --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Kabupaten / Kota</label>
                    <select class="form-select border-success-subtle filter-kota" name="kota">
                        <option value="">-- Semua Kota --</option>
                        @foreach($semuaPuskesmasMaster->pluck('nama_kabupaten')->unique() as $kota)
                            <option value="{{ $kota }}" {{ request('kota') == $kota ? 'selected' : '' }}>{{ $kota }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- KECAMATAN --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Kecamatan</label>
                    <select class="form-select border-success-subtle filter-kecamatan" name="kecamatan" {{ request('kota') ? '' : 'disabled' }}>
                        <option value="">-- Semua Kecamatan --</option>
                    </select>
                </div>

                {{-- PUSKESMAS --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Puskesmas</label>
                    <select class="form-select border-success-subtle filter-puskesmas" name="puskesmas_id" {{ request('kecamatan') ? '' : 'disabled' }}>
                        <option value="">-- Semua Puskesmas --</option>
                    </select>
                </div>

                {{-- WAKTU / PERIODE --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Berdasarkan Waktu</label>
                    <select class="form-select border-success-subtle filter-waktu" name="filter_waktu">
                        <option value="">-- Semua Waktu --</option>
                        <option value="bulan" {{ request('filter_waktu') == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                        <option value="tanggal" {{ request('filter_waktu') == 'tanggal' ? 'selected' : '' }}>Rentang Tanggal</option>
                    </select>
                </div>

                {{-- INPUT BULAN --}}
                <div class="col-md-3 input-waktu input-bulan" style="display: {{ request('filter_waktu') == 'bulan' ? 'block' : 'none' }};">
                    <label class="form-label small fw-bold text-secondary">Pilih Bulan</label>
                    <select class="form-select border-success-subtle" name="bulan">
                        @php
                            $namaBulanList = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $selectedBulan = request('bulan') ? (int) request('bulan') : (int) date('m');
                        @endphp
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $selectedBulan == $i ? 'selected' : '' }}>
                                {{ $namaBulanList[$i] }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- INPUT TANGGAL --}}
                <div class="col-md-4 input-waktu input-tanggal" style="display: {{ request('filter_waktu') == 'tanggal' ? 'block' : 'none' }};">
                    <label class="form-label small fw-bold text-secondary">Rentang Tanggal</label>
                    <div class="input-group">
                        <input type="date" class="form-control border-success-subtle" name="tgl_awal" value="{{ request('tgl_awal') }}">
                        <span class="input-group-text bg-success-subtle">s/d</span>
                        <input type="date" class="form-control border-success-subtle" name="tgl_akhir" value="{{ request('tgl_akhir') }}">
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    <button type="button" @click="window.location = '{{ url()->current() }}' + (typeof activeTab !== 'undefined' ? '?tab=' + activeTab : '')" class="btn btn-outline-secondary px-4"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-search"></i> Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
