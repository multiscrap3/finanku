@extends('layouts.app')

@section('title', 'Detail Transaksi Rutin')
@section('page-title', 'Detail Transaksi Rutin')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">

    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill {{ $recurring->jenis === 'pemasukan' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($recurring->jenis) }}
                        </span>
                        <span class="badge rounded-pill {{ $recurring->is_active ? 'bg-primary' : 'bg-secondary' }}">
                            {{ $recurring->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $recurring->keterangan }}</h5>
                    <div class="fw-bold fs-4 {{ $recurring->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($recurring->jumlah, 0, ',', '.') }}
                    </div>
                </div>
                <div class="d-flex gap-3 flex-shrink-0">
                    <a href="{{ route('recurring.edit', $recurring) }}" class="small text-primary text-decoration-none">Edit</a>
                    <form method="POST" action="{{ route('recurring.destroy', $recurring) }}"
                          onsubmit="return confirm('Hapus transaksi rutin ini?')" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:.78rem;">Hapus</button>
                    </form>
                </div>
            </div>

            <div class="row g-3 small mb-4">
                <div class="col-6">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Frekuensi</div>
                    <div class="fw-medium text-capitalize">{{ $recurring->frekuensi }}</div>
                </div>
                @if($recurring->kategori)
                    <div class="col-6">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Kategori</div>
                        <div class="fw-medium">{{ $recurring->kategori->nama }}</div>
                    </div>
                @endif
                @if($recurring->sumberTransaksi)
                    <div class="col-6">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Rekening</div>
                        <div class="fw-medium">{{ $recurring->sumberTransaksi->nama }}</div>
                    </div>
                @endif
                @if($recurring->next_run)
                    <div class="col-6">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Eksekusi Berikutnya</div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($recurring->next_run)->translatedFormat('d M Y') }}</div>
                    </div>
                @endif
            </div>

            <div class="border-top pt-3">
                <form method="POST" action="{{ route('recurring.toggle', $recurring) }}" class="d-inline">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm {{ $recurring->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                        <i class="bi bi-{{ $recurring->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                        {{ $recurring->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Riwayat transaksi --}}
    @if($recurring->transaksi && $recurring->transaksi->count())
        <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
            <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
                <h6 class="fw-semibold mb-0">Riwayat ({{ $recurring->transaksi->count() }})</h6>
            </div>
            <div class="card-body p-0">
                @foreach($recurring->transaksi->take(20) as $t)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                        <div class="flex-grow-1 small text-muted">
                            {{ $t->tanggal->translatedFormat('d M Y') }}
                        </div>
                        <div class="small fw-semibold {{ $t->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $t->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <a href="{{ route('recurring.index') }}" class="btn btn-link btn-sm text-muted text-decoration-none p-0">
        &larr; Kembali ke daftar
    </a>

</div>
</div>
@endsection
