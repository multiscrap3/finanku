@extends('layouts.app')

@section('title', 'Tambah Hutang / Piutang')
@section('page-title', 'Tambah Hutang / Piutang')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('hutang-piutang.store') }}">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-medium">Jenis <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-check border rounded p-3 {{ old('jenis') !== 'piutang' ? 'border-danger bg-danger bg-opacity-10' : 'border-2' }}" style="cursor:pointer;">
                                <input class="form-check-input" type="radio" name="jenis" value="hutang" id="jenisHutang"
                                       {{ old('jenis', 'hutang') === 'hutang' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="jenisHutang" style="cursor:pointer;">
                                    <div class="fw-medium small">Hutang</div>
                                    <div class="text-muted" style="font-size:.7rem;">Kamu yang berhutang</div>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check border rounded p-3 {{ old('jenis') === 'piutang' ? 'border-success bg-success bg-opacity-10' : 'border-2' }}" style="cursor:pointer;">
                                <input class="form-check-input" type="radio" name="jenis" value="piutang" id="jenisPiutang"
                                       {{ old('jenis') === 'piutang' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="jenisPiutang" style="cursor:pointer;">
                                    <div class="fw-medium small">Piutang</div>
                                    <div class="text-muted" style="font-size:.7rem;">Orang yang berhutang</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Nama Pihak <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pihak" value="{{ old('nama_pihak') }}" required
                           placeholder="Nama orang / perusahaan"
                           class="form-control @error('nama_pihak') is-invalid @enderror">
                    @error('nama_pihak')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Jumlah <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="jumlah" value="{{ old('jumlah') }}" min="1" step="1000" required
                               placeholder="0"
                               class="form-control @error('jumlah') is-invalid @enderror">
                        @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-medium">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium">Jatuh Tempo</label>
                        <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}" class="form-control">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Untuk keperluan apa..."
                              class="form-control">{{ old('keterangan') }}</textarea>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">Simpan</button>
                    <a href="{{ route('hutang-piutang.index') }}" class="btn btn-outline-secondary flex-fill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

