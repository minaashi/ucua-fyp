@extends('layouts.auth')

@section('content')
<div class="auth-wrapper">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="brand-header">
                <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" height="45">
                <h4 class="ms-2 mb-0 text-white fw-bold">UCUA Officer Portal</h4>
            </div>
            <div class="image-container">
                <img src="{{ asset('images/admin-auth.jpg') }}" alt="UCUA Officer Portal" class="welcome-image">
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <h3 class="text-center fw-bold mb-4">Reset Password</h3>
                <p class="text-center text-muted mb-4">Enter your new password</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-floating mb-3">
                        <input id="email" type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            name="email" 
                            value="{{ $email ?? old('email') }}" 
                            placeholder="name@example.com"
                            required 
                            autocomplete="email" 
                            autofocus>
                        <label for="email">Email Address</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input id="password" type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            name="password" 
                            placeholder="New Password"
                            required 
                            autocomplete="new-password">
                        <label for="password">New Password</label>
                        <i class="fas fa-eye password-toggle"></i>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input id="password-confirm" type="password" 
                            class="form-control" 
                            name="password_confirmation" 
                            placeholder="Confirm Password"
                            required 
                            autocomplete="new-password">
                        <label for="password-confirm">Confirm Password</label>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                        Reset Password
                    </button>

                    <div class="text-center">
                        <a href="{{ route('ucua.login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </form>
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
    // Password visibility toggle
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Find the password input in the same parent container
            const container = this.parentElement;
            const input = container.querySelector('input[type="password"], input[type="text"]');

            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        });
    });
});
</script>
@endpush
@endsection
