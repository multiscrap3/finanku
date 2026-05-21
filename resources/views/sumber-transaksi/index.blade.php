@extends('layouts.app')

@section('title', 'Sumber Dana')
@section('page-title', 'Sumber Dana')

@section('content')
<div class="row g-4 justify-content-center">
<div class="col-12 col-lg-8">

    {{-- Form tambah --}}
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-3">Tambah Sumber Dana</h6>
            <form method="POST" action="{{ route('sumber-transaksi.store') }}">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <input type="text" name="nama" value="{{ old('nama') }}" required
                               placeholder="Nama sumber (BCA Tabungan, GoPay...)"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <select name="jenis" class="form-select form-select-sm">
                            <option value="cash">Uang Tunai</option>
                            <option value="bank">Bank</option>
                            <option value="e-wallet">E-Wallet</option>
                            <option value="kartu_kredit">Kartu Kredit</option>
                            <option value="investasi">Investasi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="saldo" value="{{ old('saldo', 0) }}" min="0"
                                   placeholder="Saldo awal" class="form-control">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
            </form>
        </div>
    </div>

    {{-- Total saldo --}}
    @php $totalSaldo = collect($sumberTransaksi ?? [])->sum('saldo_saat_ini'); @endphp
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;border-left:4px solid #3b82f6;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <span class="small fw-medium text-primary">Total Saldo</span>
            <span class="fw-bold fs-6 text-primary">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Daftar --}}
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-0">
            @forelse($sumberTransaksi ?? [] as $sumber)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width:40px;height:40px;background:#f3f4f6;font-size:1.1rem;">
                        @switch($sumber->jenis)
                            @case('bank') 🏦 @break
                            @case('cash') 💵 @break
                            @case('e-wallet') 📱 @break
                            @case('investasi') 📈 @break
                            @default 💳
                        @endswitch
                    </div>
                    <div class="flex-grow-1 small">
                        <div class="fw-medium text-dark">{{ $sumber->nama }}</div>
                        <div class="text-muted text-capitalize" style="font-size:.72rem;">{{ $sumber->jenis }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold small">Rp {{ number_format($sumber->saldo_saat_ini, 0, ',', '.') }}</div>
                        <div class="d-flex gap-2 mt-1 justify-content-end">
                            <a href="{{ route('sumber-transaksi.edit', $sumber) }}" class="small text-primary text-decoration-none">Edit</a>
                            <form method="POST" action="{{ route('sumber-transaksi.destroy', $sumber) }}"
                                  onsubmit="return confirm('Hapus sumber dana ini?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:.78rem;">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-5 text-center text-muted small">Belum ada sumber dana.</div>
            @endforelse
        </div>
    </div>

</div>
</div>
@endsection
