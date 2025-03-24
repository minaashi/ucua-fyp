@extends('layouts.auth')

@section('content')
<div class="auth-wrapper">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="brand-header">
                <img src="{{ asset('images/ucua-logo.png') }}" alt="UCPU Logo" height="45">
                <h4 class="ms-2 mb-0 text-white fw-bold">UCUA Admin Portal</h4>
            </div>
            <div class="image-container">
                <img src="{{ asset('images/admin-auth.jpg') }}" alt="Admin Portal" class="welcome-image">
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <h3 class="text-center fw-bold mb-4">Admin Registration</h3>
                <p class="text-center text-muted mb-4">Create your administrator account</p>

                <form method="POST" action="{{ route('admin.register.submit') }}" class="needs-validation" novalidate>
                    @csrf

                    <!-- Name -->
                    <div class="form-floating mb-3">
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Full Name" 
                               required>
                        <label for="name">Full Name</label>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="name@example.com" 
                               required>
                        <label for="email">Email Address</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-3 position-relative">
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

                    <!-- Confirm Password -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Confirm Password" 
                               required>
                        <label for="password_confirmation">Confirm Password</label>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                        Register as Administrator
                    </button>

                    <div class="text-center">
                        <a href="{{ route('admin.login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Admin Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy, Dr Cik Feresa Mohd Foozy</p>
    </footer>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
});
</script>
@endpush
@endsection 