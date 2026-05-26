@extends('layouts.app')

@section('title', __('kategori.add'))
@section('page-title', __('kategori.add'))

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
                    <label class="form-label fw-medium">{{ __('kategori.name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="255"
                           placeholder="{{ __('kategori.name_ph') }}"
                           class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">{{ __('kategori.type') }} <span class="text-danger">*</span></label>
                    <select name="jenis" required class="form-select @error('jenis') is-invalid @enderror">
                        <option value="">{{ __('kategori.type') }}</option>
                        <option value="pengeluaran" {{ old('jenis') === 'pengeluaran' ? 'selected' : '' }}>{{ __('kategori.expense') }}</option>
                        <option value="pemasukan"   {{ old('jenis') === 'pemasukan'   ? 'selected' : '' }}>{{ __('kategori.income') }}</option>
                        <option value="transfer"    {{ old('jenis') === 'transfer'    ? 'selected' : '' }}>{{ __('kategori.both') }}</option>
                    </select>
                    @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label fw-medium">{{ __('kategori.icon') }}</label>
                        <input type="text" name="icon" value="{{ old('icon') }}" maxlength="10"
                               placeholder="ðŸ½ï¸" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium">{{ __('kategori.color') }}</label>
                        <input type="color" name="warna" value="{{ old('warna', '#6B7280') }}"
                               class="form-control form-control-color w-100">
                    </div>
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

