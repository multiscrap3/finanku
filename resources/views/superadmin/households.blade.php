@extends('layouts.superadmin')

@section('title', __('superadmin.households'))
@section('page-title', __('superadmin.households'))

@section('content')
<div class="row g-4">

    {{-- Filter --}}
    <div class="col-12">
        <form method="GET" class="d-flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('superadmin.search') }}"
                   class="form-control form-control-sm" style="max-width:240px;">
            <select name="status" class="form-select form-select-sm" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
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
                            <th>{{ __('superadmin.name') }}</th>
                            <th>{{ __('superadmin.status') }}</th>
                            <th>Plan</th>
                            <th>Anggota</th>
                            <th>{{ __('superadmin.created') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($households as $household)
                            <tr>
                                <td class="fw-medium">{{ $household->nama }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $household->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $household->status }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $household->plan?->nama ?? 'Free' }}</td>
                                <td class="text-muted">{{ $household->users_count }}</td>
                                <td class="text-muted">{{ $household->created_at->translatedFormat('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('superadmin.household-show', $household) }}" class="small text-primary text-decoration-none">{{ __('superadmin.view') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada household.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top px-4 py-3" style="border-radius:0 0 .75rem .75rem;">
                {{ $households->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
@endsection
