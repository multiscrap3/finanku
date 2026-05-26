@extends('layouts.app')

@section('title', __('privacy.data_export'))
@section('page-title', __('privacy.data_export'))

@section('content')
<div class="row g-4 justify-content-center">
<div class="col-12 col-lg-8">

    {{-- Info status consent --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-shield-check text-success me-2"></i>Status Persetujuan Data
            </h6>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="bg-light rounded p-3">
                        <div class="small text-muted mb-1">Consent Diberikan</div>
                        <div class="fw-semibold">
                            @if($user->consent_given_at)
                                {{ $user->consent_given_at->format('d M Y, H:i') }}
                            @else
                                <span class="text-warning">Belum tercatat</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="bg-light rounded p-3">
                        <div class="small text-muted mb-1">Versi Kebijakan</div>
                        <div class="fw-semibold">
                            {{ $user->privacy_policy_version ?? '—' }}
                            <a href="{{ route('privacy.policy') }}" class="small text-primary ms-2">Lihat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat consent --}}
    @if($consentLogs->count() > 0)
    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Persetujuan
            </h6>
            <div class="table-responsive">
                <table class="table table-sm small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Versi Kebijakan</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consentLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($log->type === 'register')
                                    <span class="badge bg-success-subtle text-success">Registrasi</span>
                                @elseif($log->type === 'update')
                                    <span class="badge bg-primary-subtle text-primary">Pembaruan</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $log->type }}</span>
                                @endif
                            </td>
                            <td>v{{ $log->policy_version }}</td>
                            <td class="text-muted">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Hak Subjek Data --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-person-check text-primary me-2"></i>Hak Anda atas Data Pribadi
            </h6>
            <p class="small text-muted mb-4">
                Sesuai UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi, Anda memiliki hak-hak berikut:
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex gap-3 p-3 bg-light rounded">
                        <div class="flex-shrink-0">
                            <i class="bi bi-eye text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Hak Akses</div>
                            <div class="small text-muted">Lihat data pribadi yang kami simpan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-3 p-3 bg-light rounded">
                        <div class="flex-shrink-0">
                            <i class="bi bi-download text-success fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Hak Portabilitas</div>
                            <div class="small text-muted">Unduh semua data dalam format JSON</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-3 p-3 bg-light rounded">
                        <div class="flex-shrink-0">
                            <i class="bi bi-pencil text-warning fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Hak Koreksi</div>
                            <div class="small text-muted">Perbarui data via <a href="{{ route('settings.index') }}?tab=profil">Settings &rarr; Profil</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-3 p-3 bg-light rounded">
                        <div class="flex-shrink-0">
                            <i class="bi bi-trash text-danger fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Hak Hapus</div>
                            <div class="small text-muted">Hapus akun via <a href="{{ route('settings.index') }}?tab=profil">Settings &rarr; Hapus Akun</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Unduh Data --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem; border-left: 4px solid #1a73e8 !important;">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-2">
                <i class="bi bi-file-earmark-arrow-down text-primary me-2"></i>{{ __('privacy.data_download') }}
            </h6>
            <p class="small text-muted mb-3">
                Dapatkan salinan semua data pribadi Anda dalam format JSON, termasuk profil, transaksi,
                pengaturan, dan riwayat persetujuan.
            </p>
            <a href="{{ route('privacy.download') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-download me-2"></i>{{ __('privacy.download_json') }}
            </a>
        </div>
    </div>

    {{-- Kontak --}}
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-2">
                <i class="bi bi-envelope text-secondary me-2"></i>{{ __('privacy.contact') }}
            </h6>
            <p class="small text-muted mb-1">
                Pertanyaan tentang data atau permintaan hak subjek data:
            </p>
            <a href="mailto:finanku.app@gmail.com" class="small">
                finanku.app@gmail.com
            </a>
            <p class="small text-muted mt-2 mb-0">
                Kami merespons dalam <strong>14 hari kerja</strong> sesuai UU PDP.
            </p>
        </div>
    </div>

</div>
</div>
@endsection
