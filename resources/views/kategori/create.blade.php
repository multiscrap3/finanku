@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('kategori.store') }}">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-medium">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="255"
                           placeholder="contoh: Makanan"
                           class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Jenis <span class="text-danger">*</span></label>
                    <select name="jenis" required class="form-select @error('jenis') is-invalid @enderror">
                        <option value="">Pilih jenis</option>
                        <option value="pengeluaran" {{ old('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="pemasukan"   {{ old('jenis') === 'pemasukan'   ? 'selected' : '' }}>Pemasukan</option>
                        <option value="transfer"    {{ old('jenis') === 'transfer'    ? 'selected' : '' }}>Transfer</option>
                    </select>
                    @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label fw-medium">Icon (emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon') }}" maxlength="10"
                               placeholder="ðŸ½ï¸" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium">Warna</label>
                        <input type="color" name="warna" value="{{ old('warna', '#6B7280') }}"
                               class="form-control form-control-color w-100">
                    </div>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">Tambah Kategori</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary flex-fill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

