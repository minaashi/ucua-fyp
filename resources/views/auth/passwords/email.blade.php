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
                <p class="text-center text-muted mb-4">Enter your email address to receive a password reset link</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-floating mb-4">
                        <input id="email" type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            name="email" 
                            value="{{ old('email') }}" 
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

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                        Send Password Reset Link
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
@endsection
