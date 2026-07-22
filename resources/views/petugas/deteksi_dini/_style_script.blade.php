{{-- ============================================================
     _style_script.blade.php
     Shared CSS + JS untuk create.blade.php & edit.blade.php
     ============================================================ --}}

<style>
/* ---- Layout Wrapper ---- */
.ptm-wrap {
    max-width: 1140px;
    margin: 0 auto;
    padding: 28px 24px 60px;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    color: #1e293b;
}

/* ---- Page Header ---- */
.ptm-page-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 28px;
}
.ptm-page-icon {
    width: 50px; height: 50px;
    background: #e8f9f8;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ptm-page-title {
    font-size: 1.55rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.ptm-page-sub {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
}

/* ---- Alert ---- */
.ptm-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.88rem;
    color: #991b1b;
    margin-bottom: 20px;
}

/* ---- Card ---- */
.ptm-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.05);
    padding: 32px 36px;
    margin-bottom: 20px;
}

/* ---- Section Title ---- */
.ptm-section-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ptm-num {
    width: 28px; height: 28px;
    background: #2aa8a0;
    color: #fff;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.85rem; font-weight: 700;
    flex-shrink: 0;
}

/* ---- Labels ---- */
.ptm-label     { font-size: 0.93rem; font-weight: 600; color: #374151; margin-bottom: 7px; display: block; }
.ptm-label-sm  { font-size: 0.88rem; font-weight: 600; color: #4b5563; margin-bottom: 6px; display: block; }
.ptm-sublabel  { font-size: 0.92rem; font-weight: 600; color: #374151; margin-bottom: 10px; }
.ptm-req       { color: #ef4444; }
.ptm-opt       { font-weight: 400; color: #94a3b8; font-size: 0.83rem; }
.ptm-desc      { font-size: 0.88rem; color: #64748b; margin-bottom: 16px; margin-top: -12px; }
.ptm-field     { margin-bottom: 16px; }

/* ---- Inputs ---- */
.ptm-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    font-size: 0.95rem;
    color: #1e293b;
    background: #fff;
    transition: border-color .2s;
    outline: none;
}
.ptm-input:focus { border-color: #2aa8a0; box-shadow: 0 0 0 3px rgba(42,168,160,.1); }
.ptm-input.ptm-readonly { background: #f8fafc; color: #64748b; cursor: default; }
.ptm-input.is-invalid   { border-color: #ef4444; }

/* ---- Select ---- */
.ptm-select {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    font-size: 0.95rem;
    color: #1e293b;
    background: #fff;
    transition: border-color .2s;
    outline: none;
    cursor: pointer;
    appearance: auto;
}
.ptm-select:focus { border-color: #2aa8a0; box-shadow: 0 0 0 3px rgba(42,168,160,.1); }
.ptm-select-teal { border-color: #2aa8a0; }

/* ---- Input with unit suffix ---- */
.ptm-input-unit {
    position: relative;
    display: flex;
    align-items: center;
}
.ptm-input-unit .ptm-input { padding-right: 54px; }
.ptm-unit {
    position: absolute;
    right: 10px;
    font-size: 0.78rem;
    color: #94a3b8;
    font-weight: 500;
    pointer-events: none;
}

/* ---- IMT Box ---- */
.ptm-imt-box {
    display: flex;
    align-items: stretch;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    overflow: hidden;
}
.ptm-imt-val {
    flex: 1;
    border: none;
    padding: 11px 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    background: #f8fafc;
    outline: none;
}
.ptm-imt-label {
    padding: 11px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border-left: 1.5px solid #e2e8f0;
    white-space: nowrap;
    display: flex; align-items: center;
}

/* ---- Info Box (Pasien) ---- */
.ptm-info-box {
    background: #f0faf9;
    border: 1px solid #b2e4e1;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.85rem;
    color: #1e293b;
    margin-bottom: 14px;
}
.ptm-info-label { color: #64748b; }

/* ---- Auto Badge Row ---- */
.ptm-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 6px;
    min-height: 34px;
    align-items: center;
}
.ptm-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 600;
    white-space: nowrap;
}
.ptm-badge .ptm-chk-icon {
    width: 16px; height: 16px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
}
/* Badge colors */
.ptm-badge-teal   { background:#e6f8f7; color:#0e7673; }
.ptm-badge-teal   .ptm-chk-icon { background:#2aa8a0; }
.ptm-badge-red    { background:#fef2f2; color:#991b1b; }
.ptm-badge-red    .ptm-chk-icon { background:#ef4444; }
.ptm-badge-amber  { background:#fffbeb; color:#92400e; }
.ptm-badge-amber  .ptm-chk-icon { background:#f59e0b; }
.ptm-badge-blue   { background:#eff6ff; color:#1d4ed8; }
.ptm-badge-blue   .ptm-chk-icon { background:#3b82f6; }

.ptm-empty-msg { font-size: 0.82rem; color: #94a3b8; margin-bottom: 0; }

/* ---- Divider ---- */
.ptm-divider { border-color: #e2e8f0; margin: 20px 0; }

/* ---- Manual Checkbox ---- */
.ptm-chk-label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    cursor: pointer;
    font-size: 0.88rem;
    color: #374151;
    background: #fff;
    transition: all .15s;
    width: 100%;
    user-select: none;
}
.ptm-chk-label:hover { border-color: #2aa8a0; background: #f0faf9; }
.ptm-chk-label:has(.ptm-chk-input:checked) {
    border-color: #2aa8a0;
    background: #e6f8f7;
    color: #0e7673;
    font-weight: 600;
}
.ptm-chk-input { width: 16px; height: 16px; flex-shrink: 0; accent-color: #2aa8a0; }

/* ---- Actions ---- */
.ptm-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 14px;
    padding: 12px 0 12px;
}
.ptm-btn-back {
    padding: 12px 28px;
    border-radius: 9px;
    border: 1.5px solid #cbd5e1;
    background: #fff;
    color: #64748b;
    font-size: 0.93rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;
}
.ptm-btn-back:hover { border-color: #94a3b8; color: #374151; }
.ptm-btn-save {
    padding: 12px 36px;
    border-radius: 9px;
    border: none;
    background: #2aa8a0;
    color: #fff;
    font-size: 0.93rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
    letter-spacing: 0.02em;
}
.ptm-btn-save:hover { background: #1f8f87; }

/* ---- Background ---- */
body { background-color: #f1f5f9 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ========== Toggle Kriteria ========== */
    const btnToggleKriteria = document.getElementById('btnToggleKriteria');
    const kriteriaCollapse = document.getElementById('kriteriaCollapse');
    if (btnToggleKriteria && kriteriaCollapse) {
        btnToggleKriteria.addEventListener('click', function(e) {
            e.preventDefault();
            if (kriteriaCollapse.style.display === 'none' || kriteriaCollapse.style.display === '') {
                kriteriaCollapse.style.display = 'block';
            } else {
                kriteriaCollapse.style.display = 'none';
            }
        });
    }

    /* ========== Info Pasien (create only) ========== */
    const pesertaSel = document.getElementById('peserta_id');
    const infoPasien = document.getElementById('info-pasien');
    if (pesertaSel && infoPasien) {
        function updateInfo() {
            const opt = pesertaSel.options[pesertaSel.selectedIndex];
            if (opt && opt.value) {
                document.getElementById('i-nama').textContent    = opt.dataset.nama    || '-';
                document.getElementById('i-nik').textContent     = opt.dataset.nik     || '-';
                document.getElementById('i-lahir').textContent   = opt.dataset.lahir   || '-';
                document.getElementById('i-kelamin').textContent = opt.dataset.kelamin || '-';
                infoPasien.classList.remove('d-none');
            } else {
                infoPasien.classList.add('d-none');
            }
        }
        pesertaSel.addEventListener('change', updateInfo);
        updateInfo();
    }

    /* ========== Refs ========== */
    const sistolikEl  = document.getElementById('sistolik');
    const diastolikEl = document.getElementById('diastolik');
    const gulaEl      = document.getElementById('gula_darah');
    const beratEl     = document.getElementById('berat_badan');
    const tinggiEl    = document.getElementById('tinggi_badan');
    const kolEl       = document.getElementById('kolesterol');

    const imtDisplay  = document.getElementById('imt_display');
    const imtLabel    = document.getElementById('imt_label');
    const imtHidden   = document.getElementById('imt_hidden');
    const tensiHidden = document.getElementById('tekanan_darah_hidden');

    const cbHiper  = document.getElementById('cbHipertensi');
    const cbDM     = document.getElementById('cbDiabetes');
    const cbObesas = document.getElementById('cbObesitas');
    const cbNormal = document.getElementById('cbNormal');

    const badgeRow     = document.getElementById('auto-badge-container');
    const noPTMMsg     = document.getElementById('no-ptm-msg');

    /* ========== Helpers ========== */
    function num(el) {
        if (!el || el.value === '') return NaN;
        return parseFloat(el.value);
    }

    function imtKategori(imt) {
        if (imt < 18.5) return { label: 'Berat Kurang', cls: 'ptm-badge-blue' };
        if (imt < 25.0) return { label: 'Normal',       cls: 'ptm-badge-teal' };
        if (imt < 27.0) return { label: 'Overweight',   cls: 'ptm-badge-amber' };
        if (imt < 30.0) return { label: 'Obesitas I',   cls: 'ptm-badge-red' };
        return              { label: 'Obesitas II',  cls: 'ptm-badge-red' };
    }

    function makeBadge(label, cls) {
        return `<span class="ptm-badge ${cls}">
                    <span class="ptm-chk-icon">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <path d="M2 5l2.5 2.5L8 3" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    ${label}
                </span>`;
    }

    /* ========== Main Calculation ========== */
    function calculate() {
        const sistole  = num(sistolikEl);
        const diastole = num(diastolikEl);
        const gula     = num(gulaEl);
        const berat    = num(beratEl);
        const tinggi   = num(tinggiEl);
        const kol      = num(kolEl);

        /* --- IMT --- */
        let imt = null;
        if (!isNaN(berat) && !isNaN(tinggi) && tinggi > 0) {
            const tinggiM = tinggi < 3 ? tinggi : tinggi / 100;
            imt = berat / (tinggiM * tinggiM);
        }

        if (imt !== null) {
            const kat = imtKategori(imt);
            imtDisplay.value      = imt.toFixed(1);
            imtHidden.value       = imt.toFixed(2);
            imtLabel.textContent  = kat.label;
            // update imt-label color
            imtLabel.className = 'ptm-imt-label';
            if (kat.cls === 'ptm-badge-teal')  imtLabel.style.color = '#0e7673';
            else if (kat.cls === 'ptm-badge-amber') imtLabel.style.color = '#92400e';
            else if (kat.cls === 'ptm-badge-red')   imtLabel.style.color = '#991b1b';
            else imtLabel.style.color = '#1d4ed8';
        } else {
            imtDisplay.value = '';
            imtHidden.value  = '';
            imtLabel.textContent = '—';
            imtLabel.style.color = '#64748b';
        }

        /* --- Hidden tekanan darah --- */
        if (tensiHidden) {
            if (sistolikEl && diastolikEl && sistolikEl.value && diastolikEl.value) {
                tensiHidden.value = sistolikEl.value + '/' + diastolikEl.value;
            } else if (sistolikEl && sistolikEl.value) {
                tensiHidden.value = sistolikEl.value;
            }
        }

        /* --- Auto-check logic --- */
        let badges = [];
        let hasDisease = false;

        // 1. Hipertensi / Pre-Hipertensi
        const isHiper    = (!isNaN(sistole) && sistole >= 140) || (!isNaN(diastole) && diastole >= 90);
        const isPreHiper = (!isNaN(sistole) && sistole >= 130 && sistole < 140) || (!isNaN(diastole) && diastole >= 85 && diastole < 90);
        if (isHiper) {
            cbHiper.checked = true;
            badges.push(makeBadge('Terindikasi Hipertensi', 'ptm-badge-red'));
            hasDisease = true;
        } else if (isPreHiper) {
            cbHiper.checked = false;
            badges.push(makeBadge('Terindikasi Pre-Hipertensi', 'ptm-badge-amber'));
            hasDisease = true;
        } else {
            cbHiper.checked = false;
        }

        // 2. Gula Darah Tinggi
        const isGulaTinggi = !isNaN(gula) && gula >= 200;
        const isGulaBatas  = !isNaN(gula) && gula >= 140 && gula < 200;
        cbDM.checked = isGulaTinggi;
        if (isGulaTinggi) {
            badges.push(makeBadge('Terindikasi Gula Darah Tinggi', 'ptm-badge-amber'));
            hasDisease = true;
        } else if (isGulaBatas) {
            badges.push(makeBadge('Terindikasi Gula Darah Batas', 'ptm-badge-amber'));
            hasDisease = true;
        }

        // 3. Kolesterol
        if (!isNaN(kol) && kol > 200) {
            badges.push(makeBadge('Terindikasi Hiperkolesterolemia', 'ptm-badge-blue'));
            hasDisease = true;
        }

        // 4. IMT
        if (imt !== null && imt >= 25) {
            cbObesas.checked = true;
            const imtKat = imtKategori(imt);
            badges.push(makeBadge('Terindikasi ' + imtKat.label, imtKat.cls));
            hasDisease = true;
        } else {
            cbObesas.checked = false;
        }

        // 5. Normal
        const manualSelect = document.querySelector('.select2-manual');
        let anyManual = false;
        if (manualSelect && manualSelect.selectedOptions) {
            anyManual = manualSelect.selectedOptions.length > 0;
        }

        const adaIsian  = (sistolikEl && sistolikEl.value) || 
                          (diastolikEl && diastolikEl.value) || 
                          (gulaEl && gulaEl.value) || 
                          (beratEl && beratEl.value);

        if (!hasDisease && adaIsian && !anyManual) {
            cbNormal.checked = true;
            badges.push(makeBadge('Normal', 'ptm-badge-teal'));
        } else {
            cbNormal.checked = false;
        }

        /* --- Render badges --- */
        if (badgeRow) {
            if (badges.length > 0) {
                badgeRow.innerHTML = badges.join('');
                if (noPTMMsg) noPTMMsg.style.display = 'none';
            } else {
                badgeRow.innerHTML = '';
                if (noPTMMsg) noPTMMsg.style.display = 'block';
            }
        }
    }

    /* ========== Listeners & Init ========== */
    [sistolikEl, diastolikEl, gulaEl, beratEl, tinggiEl, kolEl].forEach(el => {
        if (el) {
            el.addEventListener('input', calculate);
            el.addEventListener('keyup', calculate);
        }
    });

    // Panggil calculate() sekali saat halaman dimuat (untuk edit)
    calculate();

    
    /* ========== Init Select2 ========== */
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-manual').select2({
            placeholder: "Ketik atau pilih jenis PTM (Bisa lebih dari 1)...",
            allowClear: true,
            width: '100%'
        });
        $('.select2-manual').on('change', calculate);
    }

    /* ========== Run on load ========== */
    calculate();
});
</script>
