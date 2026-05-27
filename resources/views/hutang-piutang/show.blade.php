@extends('layouts.app')

@section('title', __('hutang.detail'))
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
                            {{ $hutangPiutang->status === 'lunas' ? __('hutang.paid') : __('hutang.outstanding') }}
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $hutangPiutang->nama_pihak }}</h5>
                    @if($hutangPiutang->keterangan)
                        <p class="text-muted small mb-0">{{ $hutangPiutang->keterangan }}</p>
                    @endif
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('hutang-piutang.edit', $hutangPiutang) }}" class="small text-primary text-decoration-none">{{ __('messages.edit') }}</a>
                    <form method="POST" action="{{ route('hutang-piutang.destroy', $hutangPiutang) }}"
                          onsubmit="return confirm('{{ __('hutang.delete_confirm') }}')" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:.78rem;">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>

            <div class="row g-3 py-3 border-top border-bottom mb-3">
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size:.72rem;">{{ __('laporan.total') }}</div>
                    <div class="fw-bold fs-6">Rp {{ number_format($hutangPiutang->jumlah_total, 0, ',', '.') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size:.72rem;">{{ __('hutang.paid') }}</div>
                    <div class="fw-bold text-success fs-6">Rp {{ number_format($hutangPiutang->jumlah_terbayar, 0, ',', '.') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size:.72rem;">{{ __('hutang.outstanding') }}</div>
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
                <span>{{ number_format($persen, 0) }}% {{ __('hutang.paid') }}</span>
                @if($hutangPiutang->tanggal_jatuh_tempo)
                    <span>{{ __('hutang.due_date') }}: {{ $hutangPiutang->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Bayar / Terima form --}}
    @if($hutangPiutang->status !== 'lunas')
        <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">
                    {{ $hutangPiutang->jenis === 'hutang' ? __('hutang.add_payment_debt') : __('hutang.add_payment_credit') }}
                </h6>
                <form method="POST" action="{{ route('hutang-piutang.bayar', $hutangPiutang) }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">
                                {{ __('hutang.source') }}
                            </label>
                            <select name="sumber_transaksi_id" required class="form-select form-select-sm">
                                <option value="">{{ __('hutang.source') }}</option>
                                @foreach($sumberTransaksi as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">{{ __('hutang.amount') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah" min="1" max="{{ $hutangPiutang->sisa }}" required
                                       value="{{ $hutangPiutang->sisa }}" placeholder="0" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">{{ __('messages.date') }}</label>
                            <input type="date" name="tanggal" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">{{ __('hutang.notes') }}</label>
                            <input type="text" name="keterangan" placeholder="{{ __('hutang.notes') }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm fw-medium">
                        {{ __('hutang.mark_paid') }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Jadwal cicilan berikutnya --}}
    @if($hutangPiutang->tipe_pembayaran === 'cicilan' && $hutangPiutang->status !== 'lunas')
        <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 mb-4 small border-0 rounded-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <span>
                {{ __('hutang.next_installment') }}:
                <strong>Rp {{ number_format($hutangPiutang->jumlah_cicilan, 0, ',', '.') }}</strong>
                — {{ optional($hutangPiutang->jadwal_cicilan_berikutnya)->translatedFormat('d M Y') ?? '-' }}
                ({{ __('hutang.freq_' . $hutangPiutang->frekuensi_cicilan) }})
            </span>
        </div>
    @endif

    {{-- Riwayat pembayaran --}}
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
            <h6 class="fw-semibold mb-0">{{ __('laporan.transactions') }}</h6>
        </div>
        <div class="card-body p-0">
            @forelse($riwayat ?? [] as $r)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="flex-grow-1">
                        <div class="small fw-medium">{{ $r->keterangan ?: __('hutang.payment') }}</div>
                        <div class="text-muted" style="font-size:.72rem;">
                            {{ optional($r->tanggal)->translatedFormat('d M Y') ?? $r->created_at->translatedFormat('d M Y') }}
                            @if($r->sumberTransaksi)
                                · {{ $r->sumberTransaksi->nama }}
                            @endif
                        </div>
                    </div>
                    <div class="small fw-semibold text-success me-2">
                        Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pembayaran.edit', $r) }}"
                           class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.72rem;">
                            {{ __('messages.edit') }}
                        </a>
                        <form method="POST" action="{{ route('pembayaran.destroy', $r) }}"
                              onsubmit="return confirm('{{ __('hutang.delete_payment_confirm') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size:.72rem;">
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-4 text-center text-muted small">{{ __('messages.no_data') }}</div>
            @endforelse
        </div>
    </div>

</div>
</div>
@endsection
