@extends('layouts.app')

@section('title', __('sumber.edit'))
@section('page-title', __('sumber.edit'))

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('sumber-transaksi.update', $sumberTransaksi) }}">
                @csrf @method('PUT')

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-4">
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-medium">{{ __('sumber.name') }}</label>
                    <input type="text" name="nama" value="{{ old('nama', $sumberTransaksi->nama) }}" required
                           class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">{{ __('sumber.type') }}</label>
                    <select name="jenis" class="form-select">
                        <option value="cash"         {{ old('jenis', $sumberTransaksi->jenis) === 'cash'         ? 'selected' : '' }}>{{ __('sumber.cash') }}</option>
                        <option value="bank"         {{ old('jenis', $sumberTransaksi->jenis) === 'bank'         ? 'selected' : '' }}>{{ __('sumber.bank') }}</option>
                        <option value="e-wallet"     {{ old('jenis', $sumberTransaksi->jenis) === 'e-wallet'     ? 'selected' : '' }}>{{ __('sumber.ewallet') }}</option>
                        <option value="kartu_kredit" {{ old('jenis', $sumberTransaksi->jenis) === 'kartu_kredit' ? 'selected' : '' }}>{{ __('sumber.other') }}</option>
                        <option value="investasi"    {{ old('jenis', $sumberTransaksi->jenis) === 'investasi'    ? 'selected' : '' }}>{{ __('sumber.investment') }}</option>
                        <option value="lainnya"      {{ old('jenis', $sumberTransaksi->jenis) === 'lainnya'      ? 'selected' : '' }}>{{ __('sumber.other') }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">{{ __('sumber.current_balance') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" readonly
                               value="{{ number_format($sumberTransaksi->saldo_saat_ini, 0, ',', '.') }}"
                               class="form-control bg-light text-muted">
                    </div>
                    <div class="form-text">{{ __('sumber.notes') }}</div>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">{{ __('sumber.save') }}</button>
                    <a href="{{ route('sumber-transaksi.index') }}" class="btn btn-outline-secondary flex-fill">{{ __('sumber.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

