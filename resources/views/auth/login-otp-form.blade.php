@extends('layouts.auth')

@section('content')
<div class="auth-wrapper {{ $userType === 'department' ? 'department-auth' : ($userType === 'ucua' ? 'ucua-auth' : ($userType === 'admin' ? 'admin-auth' : 'user-auth')) }}">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <div class="brand-header">
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="UCUA Logo" height="40">
                    </div>
                </div>
                <h3 class="text-center fw-bold mb-4">Enter Login OTP</h3>
                <p class="text-center text-muted mb-4">
                    We've sent a verification code to your email address. Please enter it below to complete your login.
                </p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.otp.verify') }}" data-ucua-form data-ucua-options='{"loadingText": "Verifying OTP..."}'>
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="user_type" value="{{ $userType }}">

                    <div class="form-floating mb-3">
                        <input id="otp" type="text" 
                            class="form-control @error('otp') is-invalid @enderror" 
                            name="otp" required autofocus maxlength="6"
                            placeholder="Enter OTP">
                        <label for="otp">One-Time Password (OTP)</label>
                        @error('otp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="alert alert-info mb-4">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            The OTP will expire in 5 minutes for security purposes.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                        <i class="fas fa-shield-alt me-2"></i>Verify OTP & Login
                    </button>
                </form>

                <div class="text-center mb-0">
                    <p class="mb-2">Didn't receive the OTP?</p>
                    <form method="POST" action="{{ route('login.otp.resend') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="user_type" value="{{ $userType }}">
                        <button type="submit" class="btn btn-link text-primary text-decoration-none fw-bold p-0">
                            <i class="fas fa-redo me-1"></i>Resend OTP
                        </button>
                    </form>
                </div>

                <hr class="my-4">

                <div class="text-center">
                    @if($userType === 'admin')
                        <a href="{{ route('admin.login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Admin Login
                        </a>
                    @elseif($userType === 'ucua')
                        <a href="{{ route('ucua.login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to UCUA Login
                        </a>
                    @elseif($userType === 'department')
                        <a href="{{ route('department.login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Department Login
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to User Login
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
    
    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy</p>
    </footer>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on OTP input
    document.getElementById('otp').focus();
    
    // Auto-submit when 6 characters are entered
    document.getElementById('otp').addEventListener('input', function() {
        if (this.value.length === 6) {
            // Optional: Auto-submit after a short delay
            // setTimeout(() => this.form.submit(), 500);
        }
    });
});
</script>
@endpush
@endsection
