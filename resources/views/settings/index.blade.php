@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
@php $activeTab = request('tab', 'profil'); @endphp
<div class="row g-4 justify-content-center">
<div class="col-12 col-lg-8">

    {{-- Nav tabs — scrollable horizontal di mobile --}}
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;margin-bottom:1.25rem;">
        <ul class="nav nav-pills flex-nowrap gap-1 pb-1" id="settingsTabs" role="tablist"
            style="min-width:max-content;">
            @foreach(['profil' => 'Profil', 'password' => 'Password', 'household' => 'Household', 'preferensi' => 'Preferensi', 'privasi' => 'Privasi & Data'] as $t => $label)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === $t ? 'active' : '' }}"
                            id="{{ $t }}-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#tab-{{ $t }}"
                            type="button" role="tab"
                            style="white-space:nowrap;">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-content">

        {{-- Profil --}}
        <div class="tab-pane fade {{ $activeTab === 'profil' ? 'show active' : '' }}" id="tab-profil" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-semibold mb-4">Ubah Profil</h6>
                    <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                   class="form-control">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                   class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="tab-password" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-semibold mb-4">Ubah Password</h6>
                    <form method="POST" action="{{ route('settings.password.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-medium">Password Lama</label>
                            <input type="password" name="current_password" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Password Baru</label>
                            <input type="password" name="password" required minlength="8" class="form-control">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" required class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Household --}}
        <div class="tab-pane fade {{ $activeTab === 'household' ? 'show active' : '' }}" id="tab-household" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-semibold mb-4">Pengaturan Household</h6>
                    <form method="POST" action="{{ route('settings.household.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-4">
                            <label class="form-label fw-medium">Nama Household</label>
                            <input type="text" name="nama"
                                   value="{{ old('nama', auth()->user()->household?->nama) }}" required
                                   class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Preferensi --}}
        <div class="tab-pane fade {{ $activeTab === 'preferensi' ? 'show active' : '' }}" id="tab-preferensi" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-semibold mb-4">Preferensi Tampilan</h6>
                    <form method="POST" action="{{ route('settings.preferences.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-medium">Tema</label>
                            <select name="theme" class="form-select">
                                <option value="light" {{ ($settings['theme'] ?? 'light') === 'light' ? 'selected' : '' }}>Terang</option>
                                <option value="dark" {{ ($settings['theme'] ?? '') === 'dark' ? 'selected' : '' }}>Gelap</option>
                                <option value="system" {{ ($settings['theme'] ?? '') === 'system' ? 'selected' : '' }}>Ikuti Sistem</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Bahasa</label>
                            <select name="language" class="form-select">
                                <option value="id">Bahasa Indonesia</option>
                            </select>
                        </div>

                        {{-- PDP H2: Kontrol Fitur AI --}}
                        <hr class="my-4">
                        <h6 class="fw-semibold mb-1">Kontrol Fitur AI <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:.65rem;">PDP</span></h6>
                        <p class="small text-muted mb-3">
                            Sesuai UU PDP, Anda berhak menolak pemrosesan data untuk keperluan AI.
                            Menonaktifkan fitur ini tidak mempengaruhi data transaksi Anda.
                        </p>
                        <div class="d-flex justify-content-between align-items-start p-3 border rounded mb-3">
                            <div class="me-3">
                                <div class="fw-medium small">Analisis & Insight AI</div>
                                <div class="small text-muted">Deteksi anomali transaksi dan saran keuangan otomatis</div>
                            </div>
                            <div class="form-check form-switch flex-shrink-0 mt-1">
                                <input class="form-check-input" type="checkbox" name="ai_opt_out" value="1"
                                       id="aiOptOut"
                                       {{ ($settings['ai_opt_out'] ?? '0') === '1' ? '' : 'checked' }}
                                       onchange="this.value = this.checked ? '0' : '1'">
                                <label class="form-check-label small" for="aiOptOut">Aktif</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-start p-3 border rounded mb-4">
                            <div class="me-3">
                                <div class="fw-medium small">OCR Struk Belanja</div>
                                <div class="small text-muted">Ekstraksi data dari foto struk menggunakan Google Gemini AI</div>
                            </div>
                            <div class="form-check form-switch flex-shrink-0 mt-1">
                                <input class="form-check-input" type="checkbox" name="ai_ocr_opt_out" value="1"
                                       id="aiOcrOptOut"
                                       {{ ($settings['ai_ocr_opt_out'] ?? '0') === '1' ? '' : 'checked' }}
                                       onchange="this.value = this.checked ? '0' : '1'">
                                <label class="form-check-label small" for="aiOcrOptOut">Aktif</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Preferensi</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Privasi & Data --}}
        <div class="tab-pane fade {{ $activeTab === 'privasi' ? 'show active' : '' }}" id="tab-privasi" role="tabpanel">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-semibold mb-1">Privasi & Perlindungan Data</h6>
                    <p class="small text-muted mb-4">Kelola data pribadi Anda sesuai UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP).</p>

                    {{-- Status consent --}}
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-shield-check text-success"></i>
                            <span class="fw-semibold small">Status Persetujuan</span>
                        </div>
                        @if(auth()->user()->consent_given_at)
                            <div class="small text-muted">
                                Anda telah menyetujui
                                <a href="{{ route('privacy.policy') }}" target="_blank">Kebijakan Privasi</a> v{{ auth()->user()->privacy_policy_version }}
                                pada {{ auth()->user()->consent_given_at->format('d M Y, H:i') }}.
                            </div>
                        @else
                            <div class="small text-warning">Consent belum tercatat. Hubungi support jika Anda merasa ini keliru.</div>
                        @endif
                    </div>

                    {{-- Aksi hak subjek data --}}
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <div class="fw-semibold small">Unduh Data Pribadi Saya</div>
                                <div class="small text-muted">Dapatkan salinan semua data dalam format JSON</div>
                            </div>
                            <a href="{{ route('privacy.download') }}" class="btn btn-sm btn-outline-primary flex-shrink-0">
                                <i class="bi bi-download me-1"></i>Unduh
                            </a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <div class="fw-semibold small">Riwayat Persetujuan & Hak Data</div>
                                <div class="small text-muted">Lihat log consent dan informasi hak-hak Anda</div>
                            </div>
                            <a href="{{ route('privacy.export') }}" class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                <i class="bi bi-eye me-1"></i>Lihat
                            </a>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex gap-3">
                        <a href="{{ route('privacy.policy') }}" target="_blank" class="small text-decoration-none">
                            <i class="bi bi-file-text me-1"></i>Kebijakan Privasi
                        </a>
                        <a href="{{ route('privacy.terms') }}" target="_blank" class="small text-decoration-none">
                            <i class="bi bi-file-text me-1"></i>Syarat & Ketentuan
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
