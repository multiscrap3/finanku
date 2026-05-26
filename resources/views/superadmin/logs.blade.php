@extends('layouts.superadmin')

@section('title', __('superadmin.logs'))
@section('page-title', __('superadmin.logs'))

@section('content')
<div class="row g-4">

    {{-- Filter --}}
    <div class="col-12">
        <form method="GET" class="d-flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('superadmin.search') }}"
                   class="form-control form-control-sm" style="max-width:240px;">
            <select name="household_id" class="form-select form-select-sm" style="width:auto;max-width:200px;">
                <option value="">{{ __('superadmin.households') }}</option>
                @foreach($households as $household)
                    <option value="{{ $household->id }}" {{ request('household_id') == $household->id ? 'selected' : '' }}>
                        {{ $household->nama }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">{{ __('superadmin.search') }}</button>
        </form>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th style="white-space:nowrap;">{{ __('superadmin.log_time') }}</th>
                            <th>{{ __('superadmin.log_user') }}</th>
                            <th>{{ __('superadmin.households') }}</th>
                            <th>{{ __('superadmin.log_action') }}</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-muted" style="white-space:nowrap;">{{ $log->created_at->translatedFormat('d M Y H:i') }}</td>
                                <td class="fw-medium">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="text-muted">{{ $log->household?->nama ?? '-' }}</td>
                                <td>
                                    <code class="small px-2 py-1 rounded" style="background:#f3f4f6;font-size:.72rem;">{{ $log->action }}</code>
                                </td>
                                <td class="text-muted text-truncate" style="max-width:200px;">{{ $log->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada log.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top px-4 py-3" style="border-radius:0 0 .75rem .75rem;">
                {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
@endsection
