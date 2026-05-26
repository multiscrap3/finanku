@extends('layouts.app')

@section('title', __('import.title'))
@section('page-title', __('import.title'))

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">

    {{-- Step indicator --}}
    <div class="d-flex align-items-center mb-4 gap-2">
        <div id="step1Circle" class="d-flex align-items-center justify-content-center rounded-circle fw-bold"
             style="width:28px;height:28px;font-size:.8rem;background:var(--primary);color:#fff;">1</div>
        <div class="flex-grow-1" style="height:2px;background:#e5e7eb;position:relative;">
            <div id="line1" style="height:100%;width:0%;background:var(--primary);transition:width .3s;"></div>
        </div>
        <div id="step2Circle" class="d-flex align-items-center justify-content-center rounded-circle fw-bold"
             style="width:28px;height:28px;font-size:.8rem;background:#e5e7eb;color:#9ca3af;">2</div>
        <div class="flex-grow-1" style="height:2px;background:#e5e7eb;position:relative;">
            <div id="line2" style="height:100%;width:0%;background:var(--primary);transition:width .3s;"></div>
        </div>
        <div id="step3Circle" class="d-flex align-items-center justify-content-center rounded-circle fw-bold"
             style="width:28px;height:28px;font-size:.8rem;background:#e5e7eb;color:#9ca3af;">3</div>
    </div>

    {{-- Error alert --}}
    <div id="errorAlert" class="alert alert-danger d-none mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <span id="errorMsg"></span>
    </div>

    {{-- PDP Info Banner --}}
    <div class="alert mb-4 py-3 px-4" role="alert"
         style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:.75rem;">
        <div class="d-flex gap-3">
            <i class="bi bi-shield-lock-fill text-primary flex-shrink-0 mt-1" style="font-size:1.1rem;"></i>
            <div>
                <div class="fw-semibold small mb-1" style="color:#1e40af;">Perlindungan Data Pribadi (UU PDP)</div>
                <ul class="mb-0 small" style="color:#1e3a8a;padding-left:1rem;">
                    <li>File mutasi bank Anda <strong>hanya diproses di server</strong> dan <strong>dihapus otomatis</strong> setelah import selesai.</li>
                    <li>Data transaksi yang berhasil diimpor disimpan di akun Anda dan <strong>tidak dibagikan ke pihak ketiga</strong>.</li>
                    <li>Password file Excel <strong>tidak disimpan</strong> di server — hanya digunakan sesaat untuk dekripsi.</li>
                    <li>Anda dapat menghapus riwayat import kapan saja dari halaman <a href="{{ route('import-bank.web.index') }}" class="fw-semibold" style="color:#1e40af;">Riwayat Import</a>.</li>
                </ul>
                <div class="mt-2 small">
                    <a href="{{ route('privacy.policy') }}" target="_blank" class="fw-semibold" style="color:#1e40af;">
                        <i class="bi bi-box-arrow-up-right me-1" style="font-size:.7rem;"></i>Baca Kebijakan Privasi lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 1: Upload --}}
    <div id="stepPanel1" class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                    <h5 class="fw-bold mb-1">{{ __('import.choose_bank') }}</h5>
                    <p class="text-muted small mb-0">{{ __('import.subtitle') }}</p>
                </div>
                <a href="{{ route('import-bank.web.template') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0 ms-3" download>
                    <i class="bi bi-download me-1"></i>Download Template
                </a>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Bank</label>
                <select id="bankCode" class="form-select">
                    <option value="generic">Generic (auto-detect)</option>
                    <option value="bca">BCA</option>
                    <option value="mandiri">Mandiri</option>
                    <option value="bni">BNI</option>
                    <option value="bsi">BSI</option>
                </select>
            </div>

            <div class="mb-3 d-none" id="passwordField">
                <div class="alert alert-warning py-2 px-3 mb-2 d-flex align-items-center gap-2" style="font-size:.85rem;">
                    <i class="bi bi-lock-fill"></i>
                    <span>File Excel ini dilindungi password. Masukkan password untuk melanjutkan.</span>
                </div>
                <label class="form-label fw-medium">Password File Excel</label>
                <div class="input-group">
                    <input type="password" id="excelPassword" class="form-control" placeholder="Masukkan password...">
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" tabindex="-1">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Sumber Dana (Rekening)</label>
                <select id="sumberTransaksiId" class="form-select">
                    <option value="">Pilih rekening</option>
                    @foreach($sumberTransaksi as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Kategori Default (opsional)</label>
                <select id="kategoriId" class="form-select">
                    <option value="">Tanpa kategori default</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }} ({{ ucfirst($kat->jenis) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium">{{ __('import.upload_file') }}</label>
                <label id="dropZone" class="d-flex flex-column align-items-center justify-content-center w-100 border border-dashed rounded p-4 text-center"
                       style="border-color:#d1d5db;cursor:pointer;min-height:120px;transition:border-color .2s;">
                    <div id="dropZoneEmpty">
                        <i class="bi bi-upload fs-3 text-muted d-block mb-2"></i>
                        <p class="small text-muted mb-1">Klik untuk upload atau drag &amp; drop</p>
                        <p class="text-muted mb-0" style="font-size:.72rem;">CSV, TXT, XLSX (maks. 10MB)</p>
                    </div>
                    <div id="dropZoneFilled" class="d-none">
                        <i class="bi bi-file-earmark-check fs-3 text-success d-block mb-2"></i>
                        <p class="small fw-medium text-dark mb-0" id="fileName"></p>
                    </div>
                    <input type="file" id="fileInput" class="d-none" accept=".csv,.txt,.xlsx">
                </label>
            </div>

            <button type="button" id="previewBtn" class="btn btn-primary w-100 fw-medium" disabled>
                <span id="previewBtnText">{{ __('import.preview') }}</span>
                <span id="previewBtnLoading" class="d-none">
                    <span class="spinner-border spinner-border-sm me-1"></span>Memproses...
                </span>
            </button>
        </div>
    </div>

    {{-- Step 2: Preview --}}
    <div id="stepPanel2" class="card border-0 shadow-sm d-none" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1">{{ __('import.preview') }}</h5>
                    <p class="text-muted small mb-0"><span id="previewTotal">0</span> baris ditemukan</p>
                </div>
                <button type="button" id="backToStep1" class="btn btn-link btn-sm text-muted p-0">Ganti File</button>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered" style="font-size:.78rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Jenis</th>
                        </tr>
                    </thead>
                    <tbody id="previewTbody"></tbody>
                </table>
            </div>
            <p id="moreRows" class="text-muted text-center mb-3 d-none" style="font-size:.75rem;"></p>

            <button type="button" id="importBtn" class="btn btn-success w-100 fw-medium">
                <span id="importBtnText">{{ __('import.import') }}</span>
                <span id="importBtnLoading" class="d-none">
                    <span class="spinner-border spinner-border-sm me-1"></span>Mengimport...
                </span>
            </button>
        </div>
    </div>

    {{-- Step 3: Done --}}
    <div id="stepPanel3" class="card border-0 shadow-sm d-none" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5 text-center">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
                 style="width:64px;height:64px;background:#d1fae5;">
                <i class="bi bi-check-circle-fill text-success" style="font-size:2rem;"></i>
            </div>
            <h4 class="fw-bold mb-2">{{ __('import.imported') }}!</h4>
            <p class="text-muted small mb-2" id="importResultMsg"></p>
            <p class="small mb-4" style="color:#6b7280;">
                <i class="bi bi-shield-check text-success me-1"></i>
                File mutasi bank telah dihapus dari server sesuai kebijakan perlindungan data.
            </p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('transaksi.index') }}" class="btn btn-primary px-4">
                    Lihat Transaksi
                </a>
                <button type="button" id="resetBtn" class="btn btn-outline-secondary px-4">
                    Import Lagi
                </button>
            </div>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
(function () {
    const csrfToken = document.querySelector('meta[name=csrf-token]').content;
    let currentFile = null;
    let previewRows = [];

    // ── DOM refs ─────────────────────────────────────────────
    const fileInput       = document.getElementById('fileInput');
    const dropZoneEmpty   = document.getElementById('dropZoneEmpty');
    const dropZoneFilled  = document.getElementById('dropZoneFilled');
    const fileNameEl      = document.getElementById('fileName');
    const previewBtn      = document.getElementById('previewBtn');
    const previewBtnText  = document.getElementById('previewBtnText');
    const previewBtnLoad  = document.getElementById('previewBtnLoading');
    const importBtn       = document.getElementById('importBtn');
    const importBtnText   = document.getElementById('importBtnText');
    const importBtnLoad   = document.getElementById('importBtnLoading');
    const errorAlert      = document.getElementById('errorAlert');
    const errorMsg        = document.getElementById('errorMsg');
    const previewTbody    = document.getElementById('previewTbody');
    const previewTotal    = document.getElementById('previewTotal');
    const moreRows        = document.getElementById('moreRows');
    const importResultMsg = document.getElementById('importResultMsg');
    const passwordField     = document.getElementById('passwordField');
    const excelPassword     = document.getElementById('excelPassword');
    const bankCodeEl        = document.getElementById('bankCode');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    function showStep(n) {
        [1, 2, 3].forEach(i => {
            document.getElementById('stepPanel' + i).classList.toggle('d-none', i !== n);
            const circle = document.getElementById('step' + i + 'Circle');
            if (i <= n) {
                circle.style.background = 'var(--primary)';
                circle.style.color = '#fff';
            } else {
                circle.style.background = '#e5e7eb';
                circle.style.color = '#9ca3af';
            }
        });
        document.getElementById('line1').style.width = n >= 2 ? '100%' : '0%';
        document.getElementById('line2').style.width = n >= 3 ? '100%' : '0%';
    }

    function showError(msg) {
        errorMsg.textContent = msg;
        errorAlert.classList.remove('d-none');
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function clearError() {
        errorAlert.classList.add('d-none');
    }

    function checkPreviewBtn() {
        previewBtn.disabled = !(currentFile && document.getElementById('sumberTransaksiId').value);
    }

    function showPasswordField() {
        passwordField.classList.remove('d-none');
        passwordField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => excelPassword.focus(), 400);
    }

    function hidePasswordField() {
        passwordField.classList.add('d-none');
        excelPassword.value = '';
    }

    togglePasswordBtn.addEventListener('click', function () {
        const isHidden = excelPassword.type === 'password';
        excelPassword.type = isHidden ? 'text' : 'password';
        togglePasswordIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    fileInput.addEventListener('change', function () {
        currentFile = this.files[0] || null;
        if (currentFile) {
            fileNameEl.textContent = currentFile.name;
            dropZoneEmpty.classList.add('d-none');
            dropZoneFilled.classList.remove('d-none');
        }
        clearError();
        hidePasswordField();
        checkPreviewBtn();
    });

    document.getElementById('sumberTransaksiId').addEventListener('change', checkPreviewBtn);

    previewBtn.addEventListener('click', async function () {
        if (!currentFile || !document.getElementById('sumberTransaksiId').value) return;
        clearError();
        previewBtnText.classList.add('d-none');
        previewBtnLoad.classList.remove('d-none');
        previewBtn.disabled = true;

        try {
            const fd = new FormData();
            fd.append('file', currentFile);
            fd.append('bank_code', bankCodeEl.value);
            if (excelPassword.value) fd.append('password', excelPassword.value);
            fd.append('_token', csrfToken);

            const res  = await fetch('{{ route('api.import-bank.preview') }}', { method: 'POST', body: fd });

            let json;
            try {
                json = await res.json();
            } catch (_) {
                throw new Error('Server tidak mengembalikan respons JSON. Status: ' + res.status);
            }

            console.log('[Preview Response]', res.status, json);

            if (json.password_required) {
                clearError();
                showPasswordField();
                return;
            }

            if (!res.ok || !json.success) {
                const errMsg = json.errors
                    ? Object.values(json.errors).flat().join(' ')
                    : (json.message ?? 'Gagal memproses file.');
                throw new Error(errMsg);
            }

            previewRows = json.data?.rows ?? [];
            previewTotal.textContent = json.data?.total ?? 0;

            previewTbody.innerHTML = '';
            previewRows.slice(0, 10).forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${escHtml(row.tanggal)}</td>
                    <td style="max-width:200px;" class="text-truncate">${escHtml(row.keterangan)}</td>
                    <td class="text-end fw-medium ${row.jenis === 'pemasukan' ? 'text-success' : 'text-danger'}">
                        Rp ${Number(row.jumlah).toLocaleString('id-ID')}
                    </td>
                    <td class="text-end text-capitalize">${escHtml(row.jenis)}</td>`;
                previewTbody.appendChild(tr);
            });

            if (previewRows.length > 10) {
                moreRows.textContent = '...dan ' + (previewRows.length - 10) + ' baris lainnya';
                moreRows.classList.remove('d-none');
            } else {
                moreRows.classList.add('d-none');
            }

            showStep(2);
        } catch (e) {
            showError(e.message);
        } finally {
            previewBtnText.classList.remove('d-none');
            previewBtnLoad.classList.add('d-none');
            previewBtn.disabled = false;
        }
    });

    document.getElementById('backToStep1').addEventListener('click', function () {
        clearError();
        showStep(1);
    });

    importBtn.addEventListener('click', async function () {
        clearError();
        importBtnText.classList.add('d-none');
        importBtnLoad.classList.remove('d-none');
        importBtn.disabled = true;

        try {
            const fd = new FormData();
            fd.append('file', currentFile);
            fd.append('bank_code', bankCodeEl.value);
            fd.append('sumber_transaksi_id', document.getElementById('sumberTransaksiId').value);
            const katId = document.getElementById('kategoriId').value;
            if (katId) fd.append('kategori_id', katId);
            if (excelPassword.value) fd.append('password', excelPassword.value);
            fd.append('_token', csrfToken);

            const res  = await fetch('{{ route('api.import-bank.store') }}', { method: 'POST', body: fd });
            const json = await res.json();
            if (!res.ok || !json.success) {
                const errMsg = json.errors
                    ? Object.values(json.errors).flat().join(' ')
                    : (json.message ?? 'Import gagal.');
                throw new Error(errMsg);
            }

            importResultMsg.textContent = json.message || 'Import selesai.';
            showStep(3);
        } catch (e) {
            showError(e.message);
        } finally {
            importBtnText.classList.remove('d-none');
            importBtnLoad.classList.add('d-none');
            importBtn.disabled = false;
        }
    });

    document.getElementById('resetBtn').addEventListener('click', function () {
        currentFile = null;
        previewRows = [];
        fileInput.value = '';
        hidePasswordField();
        dropZoneEmpty.classList.remove('d-none');
        dropZoneFilled.classList.add('d-none');
        previewBtn.disabled = true;
        clearError();
        showStep(1);
    });

    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
@endpush
@endsection
