@extends('layouts.app')

@section('title', 'Detail Hutang / Piutang')
@section('page-title', ucfirst($hutangPiutang->jenis))

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-9">

    {{-- Header card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill {{ $hutangPiutang->jenis === 'hutang' ? 'bg-danger' : 'bg-success' }}">
                            {{ ucfirst($hutangPiutang->jenis) }}
                        </span>
                        <span class="badge rounded-pill {{ $hutangPiutang->status === 'lunas' ? 'bg-secondary' : 'bg-primary' }}">
                            {{ $hutangPiutang->status === 'lunas' ? 'Lunas' : 'Aktif' }}
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $hutangPiutang->nama_pihak }}</h5>
                    @if($hutangPiutang->keterangan)
                        <p class="text-muted small mb-0">{{ $hutangPiutang->keterangan }}</p>
                    @endif
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('hutang-piutang.edit', $hutangPiutang) }}" class="small text-primary text-decoration-none">Edit</a>
                    <form method="POST" action="{{ route('hutang-piutang.destroy', $hutangPiutang) }}"
                          onsubmit="return confirm('Hapus data ini?')" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:.78rem;">Hapus</button>
                    </form>
                </div>
            </div>

            <div class="row g-3 py-3 border-top border-bottom mb-3">
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Total</div>
                    <div class="fw-bold fs-6">Rp {{ number_format($hutangPiutang->jumlah_total, 0, ',', '.') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Terbayar</div>
                    <div class="fw-bold text-success fs-6">Rp {{ number_format($hutangPiutang->jumlah_terbayar, 0, ',', '.') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Sisa</div>
                    <div class="fw-bold fs-6 {{ $hutangPiutang->jenis === 'hutang' ? 'text-danger' : 'text-primary' }}">
                        Rp {{ number_format($hutangPiutang->sisa, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @php $persen = $hutangPiutang->jumlah_total > 0 ? min(100, ($hutangPiutang->jumlah_terbayar / $hutangPiutang->jumlah_total) * 100) : 0; @endphp
            <div class="progress mb-2" style="height:8px;">
                <div class="progress-bar bg-success" role="progressbar" style="width:{{ $persen }}%"></div>
            </div>
            <div class="d-flex align-items-center justify-content-between small text-muted">
                <span>{{ number_format($persen, 0) }}% terbayar</span>
                @if($hutangPiutang->tanggal_jatuh_tempo)
                    <span>Jatuh tempo: {{ $hutangPiutang->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Bayar / Terima form --}}
    @if($hutangPiutang->status !== 'lunas')
        <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">
                    {{ $hutangPiutang->jenis === 'hutang' ? 'Catat Pembayaran' : 'Catat Penerimaan' }}
                </h6>
                <form method="POST" action="{{ route('hutang-piutang.bayar', $hutangPiutang) }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">
                                {{ $hutangPiutang->jenis === 'hutang' ? 'Bayar dari' : 'Terima ke' }}
                            </label>
                            <select name="sumber_transaksi_id" required class="form-select form-select-sm">
                                <option value="">Pilih rekening</option>
                                @foreach($sumberTransaksi as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Jumlah</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah" min="1" max="{{ $hutangPiutang->sisa }}" required
                                       value="{{ $hutangPiutang->sisa }}" placeholder="0" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Keterangan</label>
                            <input type="text" name="keterangan" placeholder="Cicilan 1, Lunas..." class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm fw-medium">
                        {{ $hutangPiutang->jenis === 'hutang' ? 'Catat Pembayaran' : 'Catat Penerimaan' }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Riwayat pembayaran --}}
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
            <h6 class="fw-semibold mb-0">Riwayat Pembayaran</h6>
        </div>
        <div class="card-body p-0">
            @forelse($riwayat ?? [] as $r)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="flex-grow-1">
                        <div class="small fw-medium">{{ $r->keterangan ?: 'Pembayaran' }}</div>
                        <div class="text-muted" style="font-size:.72rem;">
                            {{ optional($r->tanggal)->translatedFormat('d M Y') ?? $r->created_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <div class="small fw-semibold text-success">
                        Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="py-4 text-center text-muted small">Belum ada pembayaran.</div>
            @endforelse
        </div>
    </div>

</div>
</div>
@endsection
