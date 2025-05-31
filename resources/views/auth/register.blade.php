@extends('layouts.auth')

@section('content')
<div class="auth-wrapper">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="brand-header">
                <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" height="45">
                <h4 class="ms-2 mb-0 text-white fw-bold">UCUA Reporting System</h4>
            </div>
            <div class="image-container">
                <img src="{{ asset('images/auth-image.jpg') }}" alt="Welcome" class="welcome-image">
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <h3 class="text-center fw-bold mb-4">Create Account</h3>
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-floating mb-3">
                        <input id="name" type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            name="name" value="{{ old('name') }}" 
                            placeholder="Full Name" required>
                        <label for="name">Full Name</label>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input id="email" type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            name="email" value="{{ old('email') }}" 
                            placeholder="name@example.com" required>
                        <label for="email">Email Address</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select id="department" name="department" 
                            class="form-control @error('department') is-invalid @enderror" 
                            required>
                            <option value="">Select Department</option>
                            <option value="operations">Operations</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="security">Security</option>
                            <option value="safety">Safety</option>
                            <option value="hr">Human Resources</option>
                            <option value="it">Information Technology</option>
                        </select>
                        <label for="department">Department</label>
                        @error('department')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input id="password" type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            name="password" placeholder="Password" 
                            minlength="12" maxlength="32" required>
                        <label for="password">Password</label>
                        <i class="fas fa-eye password-toggle" aria-hidden="true"></i>
                        <small class="form-text text-muted">
                            Password must be 12-32 characters long and include:
                            <ul class="mb-0">
                                <li>At least one uppercase letter (A-Z)</li>
                                <li>At least one lowercase letter (a-z)</li>
                                <li>At least one number (0-9)</li>
                                <li>At least one special character (!@#$%^&*)</li>
                            </ul>
                        </small>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input id="password-confirm" type="password" 
                            class="form-control" name="password_confirmation" 
                            placeholder="Confirm Password" required>
                        <label for="password-confirm">Confirm Password</label>
                        <i class="fas fa-eye password-toggle" aria-hidden="true"></i>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">Create Account</button>

                    <p class="text-center mb-0">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">Sign In</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy, Dr Cik.Feresa Binti Mohd Foozy</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelectorAll('.password-toggle');
        
        togglePassword.forEach(button => {
            button.addEventListener('click', function() {
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
