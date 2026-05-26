@extends('layouts.app')

@section('title', __('hutang.edit'))
@section('page-title', __('hutang.edit'))

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <h6 class="fw-semibold mb-4">{{ __('hutang.edit') }}</h6>
            <form method="POST" action="{{ route('hutang-piutang.update', $hutangPiutang) }}">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label small fw-medium text-muted">{{ __('hutang.type') }}</label>
                    <div class="form-control bg-light text-capitalize" style="pointer-events:none;">{{ $hutangPiutang->jenis }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">
                        {{ $hutangPiutang->jenis === 'hutang' ? __('hutang.counterparty') : __('hutang.counterparty') }}
                    </label>
                    <input type="text" name="nama_pihak"
                           value="{{ old('nama_pihak', $hutangPiutang->nama_pihak) }}"
                           maxlength="255"
                           class="form-control @error('nama_pihak') is-invalid @enderror">
                    @error('nama_pihak')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">{{ __('hutang.due_date') }}</label>
                    <input type="date" name="jatuh_tempo"
                           value="{{ old('jatuh_tempo', optional($hutangPiutang->tanggal_jatuh_tempo)->format('Y-m-d')) }}"
                           class="form-control @error('jatuh_tempo') is-invalid @enderror">
                    @error('jatuh_tempo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">{{ __('hutang.notes') }}</label>
                    <textarea name="keterangan" rows="3" maxlength="500"
                              placeholder="{{ __('hutang.notes') }}"
                              class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $hutangPiutang->keterangan) }}</textarea>
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-medium">{{ __('hutang.save') }}</button>
                    <a href="{{ route('hutang-piutang.show', $hutangPiutang) }}" class="btn btn-outline-secondary flex-fill">{{ __('hutang.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

