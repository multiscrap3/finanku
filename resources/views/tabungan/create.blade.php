@extends('layouts.app')

@section('title', 'Buat Tujuan Tabungan')
@section('page-title', 'Buat Tujuan Tabungan')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('tabungan.store') }}">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-medium">Nama Tujuan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           placeholder="Contoh: Liburan ke Bali, DP Rumah..."
                           class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Target Jumlah <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="target" value="{{ old('target') }}" min="0" step="1000" required
                               placeholder="0"
                               class="form-control @error('target') is-invalid @enderror">
                        @error('target')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Target Tanggal</label>
                    <input type="date" name="tanggal_target" value="{{ old('tanggal_target') }}"
                           class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Deskripsi</label>
                    <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..."
                              class="form-control">{{ old('keterangan') }}</textarea>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">Buat Tujuan</button>
                    <a href="{{ route('tabungan.index') }}" class="btn btn-outline-secondary flex-fill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

