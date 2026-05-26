@extends('layouts.auth')

@section('title', __('auth.create_account'))

@section('content')
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">{{ __('auth.create_account') }}</h3>
        @if($invitation)
            <div class="alert alert-info py-2 small mt-2 mb-0">
                <i class="bi bi-envelope-open me-1"></i>
                {{ __('auth.invited_to') }} <strong>{{ $invitation->household->nama }}</strong>
                {{ __('auth.as_role') }} <strong>{{ $invitation->role }}</strong>.
            </div>
        @else
            <p class="text-muted mb-0">{{ __('auth.register_subtitle') }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        @if($invitation)
            <input type="hidden" name="token" value="{{ $invitation->token }}">
        @endif

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold text-dark">{{ __('auth.full_name') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                       placeholder="{{ __('auth.full_name_placeholder') }}"
                       style="border-left:none;">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-dark">{{ __('auth.email_address') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 {{ $invitation ? 'bg-light' : '' }}">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input id="email" type="email" name="email"
                       value="{{ old('email', $invitation?->email) }}" required
                       {{ $invitation ? 'readonly' : '' }}
                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror {{ $invitation ? 'bg-light' : '' }}"
                       placeholder="email@example.com"
                       style="border-left:none;">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold text-dark">{{ __('auth.password_label') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input id="password" type="password" name="password" required
                       class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror"
                       placeholder="{{ __('auth.min_8_chars') }}"
                       style="border-left:none;">
                <button type="button" class="btn btn-outline-secondary border-start-0"
                        onclick="togglePassword('password', this)"
                        style="border-left:none;">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold text-dark">{{ __('auth.confirm_password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-lock-fill text-muted"></i>
                </span>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="form-control border-start-0 border-end-0 ps-0"
                       placeholder="{{ __('auth.repeat_password') }}"
                       style="border-left:none;">
                <button type="button" class="btn btn-outline-secondary border-start-0"
                        onclick="togglePassword('password_confirmation', this)"
                        style="border-left:none;">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        {{-- PDP Consent Checkbox --}}
        <div class="mb-4">
            <div class="form-check @error('consent') is-invalid @enderror">
                <input class="form-check-input @error('consent') is-invalid @enderror"
                       type="checkbox" name="consent" id="consent" value="1"
                       {{ old('consent') ? 'checked' : '' }} required>
                <label class="form-check-label small" for="consent">
                    {{ __('auth.consent_text') }}
                    <a href="{{ route('privacy.policy') }}" target="_blank" class="fw-semibold">{{ __('messages.privacy_policy') }}</a>
                    {{ __('auth.consent_and') }}
                    <a href="{{ route('privacy.terms') }}" target="_blank" class="fw-semibold">{{ __('messages.terms') }}</a>
                    {{ __('auth.consent_pdp') }}
                </label>
                @error('consent')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-person-plus me-2"></i>
                {{ $invitation ? __('auth.join_btn') : __('auth.register_btn') }}
            </button>
        </div>
    </form>

    <p class="text-center text-muted mb-0" style="font-size:.9rem;">
        {{ __('auth.already_have_account') }}
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color:var(--primary);">{{ __('auth.login_here') }}</a>
    </p>

    <p class="text-center mt-3 mb-0" style="font-size:.8rem;">
        <a href="{{ route('privacy.policy') }}" class="text-muted text-decoration-none me-3">{{ __('messages.privacy_policy') }}</a>
        <a href="{{ route('privacy.terms') }}" class="text-muted text-decoration-none">{{ __('messages.terms') }}</a>
    </p>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush
