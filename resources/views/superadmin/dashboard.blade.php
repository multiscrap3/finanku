@extends('layouts.superadmin')

@section('title', __('superadmin.dashboard'))
@section('page-title', __('superadmin.dashboard'))

@section('content')
<div class="row g-4">

    {{-- Stats --}}
    @foreach([
        ['label' => __('superadmin.total_households'), 'value' => $stats['total_household'],   'icon' => 'bi-house-fill',      'color' => '#8b5cf6'],
        ['label' => __('superadmin.active_today'),     'value' => $stats['household_aktif'],   'icon' => 'bi-house-check-fill','color' => '#10b981'],
        ['label' => __('superadmin.total_users'),      'value' => $stats['total_user'],        'icon' => 'bi-people-fill',     'color' => '#3b82f6'],
        ['label' => __('superadmin.active_today'),     'value' => $stats['user_aktif'],        'icon' => 'bi-person-check-fill','color' => '#14b8a6'],
        ['label' => __('superadmin.new_this_month'),   'value' => $stats['new_household_7d'],  'icon' => 'bi-house-add-fill',  'color' => '#f97316'],
        ['label' => __('superadmin.new_this_month'),   'value' => $stats['new_user_7d'],       'icon' => 'bi-person-plus-fill','color' => '#ec4899'],
    ] as $stat)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
                <div class="card-body p-3 text-center">
                    <i class="bi {{ $stat['icon'] }} mb-1" style="font-size:1.4rem;color:{{ $stat['color'] }};"></i>
                    <div class="fw-bold fs-5">{{ number_format($stat['value']) }}</div>
                    <div class="small text-muted" style="font-size:.7rem;">{{ $stat['label'] }}</div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Recent Households --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-radius:.75rem .75rem 0 0;">
                <h6 class="fw-semibold mb-0">{{ __('superadmin.households') }}</h6>
                <a href="{{ route('superadmin.households') }}" class="small text-primary text-decoration-none">{{ __('superadmin.view') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('superadmin.name') }}</th>
                            <th>{{ __('superadmin.status') }}</th>
                            <th>Plan</th>
                            <th>{{ __('superadmin.created') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentHouseholds as $household)
                            <tr>
                                <td>
                                    <a href="{{ route('superadmin.household-show', $household) }}" class="text-primary text-decoration-none fw-medium">
                                        {{ $household->nama }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $household->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $household->status }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $household->plan?->nama ?? 'Free' }}</td>
                                <td class="text-muted">{{ $household->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada household.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-radius:.75rem .75rem 0 0;">
                <h6 class="fw-semibold mb-0">{{ __('superadmin.users') }}</h6>
                <a href="{{ route('superadmin.users') }}" class="small text-primary text-decoration-none">{{ __('superadmin.view') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('superadmin.name') }}</th>
                            <th>{{ __('superadmin.email') }}</th>
                            <th>{{ __('superadmin.households') }}</th>
                            <th>{{ __('superadmin.status') }}</th>
                            <th>{{ __('superadmin.created') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td class="text-muted">{{ $user->household?->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
