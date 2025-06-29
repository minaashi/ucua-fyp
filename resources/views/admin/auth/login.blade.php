<!-- resources/views/auth/admin-login.blade.php -->
@extends('layouts.auth')

@section('content')
<div class="auth-wrapper admin-auth">
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
                <h3 class="text-center fw-bold mb-4">Administrator Login</h3>
                <p class="text-center text-muted mb-4">Access the admin dashboard</p>

                @if(Auth::check())
                    <div class="alert alert-info">
                        <p class="mb-2">You are currently logged in as: <strong>{{ Auth::user()->email }}</strong></p>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout First
                            </button>
                        </form>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" class="needs-validation" novalidate data-ucua-form data-ucua-options='{"loadingText": "Signing In..."}'>
                    @csrf

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="name@example.com" 
                               required>
                        <label for="email">Admin Email</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Password" 
                               required>
                        <label for="password">Password</label>
                        <i class="fas fa-eye password-toggle"></i>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                        Login as Administrator
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to User Login
                        </a>
                        
                        <div class="border-t border-gray-200 my-3"></div>
                    
                        <a href="{{ route('ucua.login') }}" class="btn btn-outline-primary mt-2">
                            <i class="fas fa-user-shield me-1"></i> PIC Login
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
