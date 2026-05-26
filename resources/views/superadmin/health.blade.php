@extends('layouts.superadmin')

@section('title', __('superadmin.health'))
@section('page-title', __('superadmin.health'))

@section('content')
<div class="row g-4">

    {{-- Status bar --}}
    <div class="col-12">
        <div class="d-flex align-items-center gap-3">
            @if($allOk)
                <span class="rounded-circle d-inline-block bg-success" style="width:14px;height:14px;"></span>
                <span class="fw-semibold text-success">{{ __('superadmin.health_ok') }}</span>
            @else
                <span class="rounded-circle d-inline-block bg-danger" style="width:14px;height:14px;"></span>
                <span class="fw-semibold text-danger">{{ __('superadmin.health_issue') }}</span>
            @endif
            <span class="text-muted small">{{ now()->translatedFormat('d M Y H:i:s') }}</span>
        </div>
    </div>

    {{-- Check cards --}}
    @foreach($checks as $name => $check)
        @php
            $borderColor = match($check['status']) {
                'ok'      => '#10b981',
                'warning' => '#f59e0b',
                default   => '#ef4444',
            };
            $iconClass = match($check['status']) {
                'ok'      => 'bi-check-circle-fill text-success',
                'warning' => 'bi-exclamation-triangle-fill text-warning',
                default   => 'bi-x-circle-fill text-danger',
            };
            $msgClass = match($check['status']) {
                'ok'      => 'text-success',
                'warning' => 'text-warning',
                default   => 'text-danger',
            };
        @endphp
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100"
                 style="border-radius:.75rem;border-left:4px solid {{ $borderColor }} !important;">
                <div class="card-body p-4 d-flex align-items-start gap-3">
                    <i class="bi {{ $iconClass }} flex-shrink-0" style="font-size:1.5rem;margin-top:2px;"></i>
                    <div>
                        <div class="fw-semibold text-capitalize">{{ $name }}</div>
                        <div class="small {{ $msgClass }} mt-1">{{ $check['message'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection
