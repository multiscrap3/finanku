@extends('layouts.app')

@section('title', __('kategori.edit'))
@section('page-title', __('kategori.edit'))

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('kategori.update', $kategori) }}">
                @csrf @method('PUT')

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-medium">{{ __('kategori.name') }}</label>
                    <input type="text" name="nama" value="{{ old('nama', $kategori->nama) }}" required
                           class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">{{ __('kategori.type') }}</label>
                    <select name="jenis" class="form-select">
                        <option value="pengeluaran" {{ old('jenis', $kategori->jenis) === 'pengeluaran' ? 'selected' : '' }}>{{ __('kategori.expense') }}</option>
                        <option value="pemasukan"   {{ old('jenis', $kategori->jenis) === 'pemasukan'   ? 'selected' : '' }}>{{ __('kategori.income') }}</option>
                        <option value="transfer"    {{ old('jenis', $kategori->jenis) === 'transfer'    ? 'selected' : '' }}>{{ __('kategori.both') }}</option>
                    </select>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">{{ __('kategori.save') }}</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary flex-fill">{{ __('kategori.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

