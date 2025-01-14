@extends('layouts.auth')

@section('content')
<div class="auth-wrapper">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="brand-header">
                <img src="{{ asset('images/ucua-logo.png') }}" alt="UCPU Logo" height="45">
                <h4 class="ms-2 mb-0 text-white fw-bold">UCUA Admin Registration</h4>
            </div>
            <div class="image-container">
                <img src="{{ asset('images/admin-auth.jpg') }}" alt="Admin Registration" class="welcome-image">
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <h3 class="text-center fw-bold mb-4">Admin Registration</h3>
                <p class="text-center text-muted mb-4">Create a new administrator account</p>

                <!-- Update the form action -->
                <form method="POST" action="{{ route('admin.register.submit') }}" class="needs-validation" novalidate>
                    @csrf

                    <!-- Name -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Full Name" required>
                        <label for="name">Full Name</label>
                    </div>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="name@example.com" required>
                        <label for="email">Email Address</label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password">Password</label>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" placeholder="Confirm Password" required>
                        <label for="password_confirmation">Confirm Password</label>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>

                    <!-- Admin Code -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="admin_code" name="admin_code" 
                               placeholder="Admin Registration Code" required>
                        <label for="admin_code">Admin Registration Code</label>
                        <small class="text-muted">Enter the provided admin registration code</small>
                    </div>

                    <!-- Button to Register as Admin -->
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
