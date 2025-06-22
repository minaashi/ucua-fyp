@extends('layouts.auth')

@section('content')
<div class="auth-wrapper department-auth">
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
                <h3 class="text-center fw-bold mb-4">Department Login</h3>
                <p class="text-center text-muted mb-4">Access the department dashboard</p>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('department.login.submit') }}" class="needs-validation" novalidate data-ucua-form data-ucua-options='{"loadingText": "Signing In..."}'>
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
                        <label for="email">Department Email</label>
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

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" 
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                        Login as Department
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to User Login
                        </a>

                        <div class="border-t border-gray-200 my-3"></div>

                        <p class="text-sm text-gray-600 mb-2">Are you an administrator?</p>
                        <a href="{{ route('admin.login') }}" class="btn btn-outline-secondary">
                            Admin Login <i class="fas fa-arrow-right ms-1"></i>
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

    // Enhanced form submission with loading state
    const forms = document.querySelectorAll('[data-ucua-form]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const options = JSON.parse(form.getAttribute('data-ucua-options') || '{}');

            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    ${options.loadingText || 'Processing...'}
                `;

                // Re-enable button after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 10000);
            }
        });
    });
});
</script>
@endpush
@endsection