<div class="table-responsive">
    <table class="table table-hover align-middle mb-0" style="min-width: 1300px; font-size: 0.85rem;">
        <thead class="table-light text-muted">
            <tr>
                <th class="py-3 px-3">No</th>
                <th class="py-3 px-3">Tanggal</th>
                <th class="py-3 px-3">Pasien</th>
                <th class="py-3 px-3">Umur</th>
                <th class="py-3 px-3">Jenis Kelamin</th>
                <th class="py-3 px-3 text-center">Tekanan Darah</th>
                <th class="py-3 px-3 text-center">Gula Darah</th>
                <th class="py-3 px-3 text-center">Kolesterol</th>
                <th class="py-3 px-3 text-center">IMT</th>
                <th class="py-3 px-3">Faktor Risiko</th>
                <th class="py-3 px-3">Diagnosa</th>
                <th class="py-3 px-3">Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="px-3">{{ $loop->iteration }}</td>
                    <td class="px-3">{{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
                    <td class="px-3">
                        <strong>{{ $row->peserta->nama_lengkap }}</strong><br>
                        <span class="text-muted">{{ $row->peserta->nik }}</span>
                    </td>
                    <td class="px-3">
                        {{ \Carbon\Carbon::parse($row->peserta->tanggal_lahir)->age }} Thn
                    </td>
                    <td class="px-3">
                        <span class="text-muted">{{ $row->peserta->jenis_kelamin }}</span>
                    </td>
                    <td class="px-3 text-center">
                        @php
                            // Pisahkan tekanan darah menjadi sistolik dan diastolik
                            $td = explode('/', $row->tekanan_darah);
                            $sistolik = isset($td[0]) ? (int)$td[0] : 0;
                            $diastolik = isset($td[1]) ? (int)$td[1] : 0;
                        @endphp
                        <span class="{{ $sistolik >= 140 || $diastolik >= 90 ? 'text-danger fw-bold' : '' }}">
                            {{ $row->tekanan_darah }}
                        </span>
                    </td>
                    <td class="px-3 text-center">
                        <span class="{{ $row->gula_darah > 200 ? 'text-danger fw-bold' : '' }}">
                            {{ $row->gula_darah ?? '-' }}
                        </span>
                    </td>
                    <td class="px-3 text-center">
                        <span class="{{ $row->kolesterol > 200 ? 'text-danger fw-bold' : '' }}">
                            {{ $row->kolesterol ?? '-' }}
                        </span>
                    </td>
                    <td class="px-3 text-center">
                        <span class="{{ $row->imt > 25 ? 'text-danger fw-bold' : '' }}">
                            {{ $row->imt }}
                        </span>
                    </td>
                    <td class="px-3">
                        @if(optional($row->faktorRisiko)->merokok === 'Ya')
                            <span class="badge bg-danger-subtle text-danger mb-1">Merokok</span>
                        @endif
                        @if(optional($row->faktorRisiko)->kurang_aktivitas_fisik === 'Ya')
                            <span class="badge bg-warning-subtle text-warning">Kurang Fisik</span>
                        @endif
                        @if(optional($row->faktorRisiko)->merokok !== 'Ya' && optional($row->faktorRisiko)->kurang_aktivitas_fisik !== 'Ya')
                            <span class="badge bg-success-subtle text-success">Aman</span>
                        @endif
                    </td>
                    <td class="px-3">
                        <strong>{{ $row->diagnosa_penyakit }}</strong>
                    </td>
                    <td class="px-3" style="max-width: 200px; white-space: normal;">
                        @if($row->tindakLanjut)
                            <span class="text-teal fw-bold d-block">{{ $row->tindakLanjut->jenis_tindak_lanjut }}</span>
                            <small class="text-muted text-truncate d-block">{{ $row->tindakLanjut->catatan_petugas ?? '-' }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada data pemeriksaan pada kategori ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
