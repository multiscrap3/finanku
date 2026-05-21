@extends('layouts.app')

@section('title', 'Edit Transaksi Rutin')
@section('page-title', 'Edit Transaksi Rutin')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('recurring.update', $recurring) }}">
                @csrf @method('PUT')

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label small fw-medium text-muted">Jenis</label>
                    <div class="form-control bg-light text-capitalize" style="pointer-events:none;">{{ $recurring->jenis }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $recurring->keterangan) }}"
                           class="form-control @error('keterangan') is-invalid @enderror">
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Jumlah</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="jumlah" value="{{ old('jumlah', $recurring->jumlah) }}" min="1" step="1000"
                               class="form-control @error('jumlah') is-invalid @enderror">
                        @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Frekuensi</label>
                    <select name="frekuensi" class="form-select">
                        <option value="harian"   {{ old('frekuensi', $recurring->frekuensi) === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ old('frekuensi', $recurring->frekuensi) === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan"  {{ old('frekuensi', $recurring->frekuensi) === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan"  {{ old('frekuensi', $recurring->frekuensi) === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">Pilih kategori</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id', $recurring->kategori_id) == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama }} ({{ ucfirst($kat->jenis) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Sumber Dana</label>
                    <select name="sumber_transaksi_id" class="form-select">
                        <option value="">Pilih sumber dana</option>
                        @foreach($sumberTransaksi as $s)
                            <option value="{{ $s->id }}" {{ old('sumber_transaksi_id', $recurring->sumber_transaksi_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai"
                           value="{{ old('tanggal_selesai', optional($recurring->tanggal_selesai)->format('Y-m-d')) }}"
                           class="form-control">
                    <div class="form-text">Kosongkan jika tidak ada tanggal selesai.</div>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">Simpan Perubahan</button>
                    <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary flex-fill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

