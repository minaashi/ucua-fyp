@extends('layouts.auth')

@section('content')
<div class="auth-wrapper register-auth">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="left-panel-content">
                <div class="welcome-container">
                    <div class="welcome-text">
                        <h2 class="text-white fw-bold mb-3">Welcome to UCUA Reporting System</h2>
                        <p class="text-white-50 mb-4">Join our safety management system and help create a safer workplace for everyone.</p>
                        <div class="feature-list">
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure & Reliable</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-leaf"></i>
                                <span>Safe Environment</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Real-time Reporting</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <div class="brand-header">
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="UCUA Logo" height="40">
                    </div>
                </div>
                <div class="welcome-section text-center mb-4">
                    <h3 class="fw-bold mb-2" style="background: linear-gradient(135deg, #3b82f6, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Create Account</h3>
                    <p class="text-muted mb-0">Join the UCUA Safety Management System</p>
                </div>
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
                        <select id="department" name="department_id"
                            class="form-control @error('department') is-invalid @enderror"
                            required onchange="updateWorkerIdSuggestion()">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                        data-prefix="{{ $department->worker_id_identifier ?? 'PW' }}"
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="department">Department</label>
                        @error('department')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input id="worker_id" type="text"
                            class="form-control @error('worker_id') is-invalid @enderror"
                            name="worker_id" value="{{ old('worker_id') }}"
                            placeholder="Worker ID" required>
                        <label for="worker_id">Worker ID</label>
                        <div id="worker-id-help" class="form-text text-muted mt-1" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            <span id="worker-id-format">Select a department to see the expected format</span>
                        </div>
                        @error('worker_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input id="phone" type="tel"
                            class="form-control @error('phone') is-invalid @enderror"
                            name="phone" value="{{ old('phone') }}"
                            placeholder="Phone Number" required>
                        <label for="phone">Phone Number</label>
                        <small class="form-text text-muted">Format: +60123456789 or 0123456789</small>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Section with Enhanced UI -->
                    <div class="password-section mb-4">
                        <div class="form-floating mb-3 position-relative">
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" placeholder="Password"
                                minlength="12" maxlength="32" required>
                            <label for="password">Password</label>
                            <i class="fas fa-eye password-toggle" data-target="password" aria-hidden="true"></i>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password Strength Meter -->
                        <div class="password-strength-container mb-3">
                            <div class="password-strength-label">
                                <span class="strength-text">Password Strength: </span>
                                <span class="strength-level" id="strength-level">Weak</span>
                            </div>
                            <div class="password-strength-meter">
                                <div class="strength-bar" id="strength-bar"></div>
                            </div>
                        </div>

                        <!-- Password Requirements Card -->
                        <div class="password-requirements-card">
                            <div class="requirements-header">
                                <i class="fas fa-shield-alt"></i>
                                <span>Password Requirements</span>
                            </div>
                            <div class="requirements-list">
                                <div class="requirement-item" data-requirement="length">
                                    <i class="fas fa-times-circle requirement-icon"></i>
                                    <span class="requirement-text">12-32 characters long</span>
                                </div>
                                <div class="requirement-item" data-requirement="uppercase">
                                    <i class="fas fa-times-circle requirement-icon"></i>
                                    <span class="requirement-text">At least one uppercase letter (A-Z)</span>
                                </div>
                                <div class="requirement-item" data-requirement="lowercase">
                                    <i class="fas fa-times-circle requirement-icon"></i>
                                    <span class="requirement-text">At least one lowercase letter (a-z)</span>
                                </div>
                                <div class="requirement-item" data-requirement="number">
                                    <i class="fas fa-times-circle requirement-icon"></i>
                                    <span class="requirement-text">At least one number (0-9)</span>
                                </div>
                                <div class="requirement-item" data-requirement="special">
                                    <i class="fas fa-times-circle requirement-icon"></i>
                                    <span class="requirement-text">At least one special character (!@#$%^&*)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input id="password-confirm" type="password"
                            class="form-control" name="password_confirmation"
                            placeholder="Confirm Password" required>
                        <label for="password-confirm">Confirm Password</label>
                        <i class="fas fa-eye password-toggle" data-target="password-confirm" aria-hidden="true"></i>
                        <div class="password-match-indicator" id="password-match-indicator" style="display: none;">
                            <i class="fas fa-check-circle text-success"></i>
                            <span class="text-success">Passwords match</span>
                        </div>
                        <div class="password-mismatch-indicator" id="password-mismatch-indicator" style="display: none;">
                            <i class="fas fa-times-circle text-danger"></i>
                            <span class="text-danger">Passwords do not match</span>
                        </div>
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
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Registration page JavaScript loaded');

        // Password visibility toggle functionality
        const toggleButtons = document.querySelectorAll('.password-toggle');
        console.log('Found toggle buttons:', toggleButtons.length);

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                console.log('Toggle clicked for:', targetId);

                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                    console.log('Password visibility toggled to:', type);
                } else {
                    console.error('Input not found for target:', targetId);
                }
            });
        });

        // Password strength and requirements validation
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password-confirm');
        const strengthBar = document.getElementById('strength-bar');
        const strengthLevel = document.getElementById('strength-level');
        const matchIndicator = document.getElementById('password-match-indicator');
        const mismatchIndicator = document.getElementById('password-mismatch-indicator');

        console.log('Password elements found:', {
            passwordInput: !!passwordInput,
            confirmPasswordInput: !!confirmPasswordInput,
            strengthBar: !!strengthBar,
            strengthLevel: !!strengthLevel,
            matchIndicator: !!matchIndicator,
            mismatchIndicator: !!mismatchIndicator
        });

        // Password requirements validation
        function validatePassword(password) {
            console.log('Validating password:', password.length, 'characters');

            const requirements = {
                length: password.length >= 12 && password.length <= 32,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*]/.test(password)
            };

            console.log('Requirements met:', requirements);

            // Update requirement indicators
            Object.keys(requirements).forEach(req => {
                const item = document.querySelector(`[data-requirement="${req}"]`);
                if (!item) {
                    console.error('Requirement item not found:', req);
                    return;
                }

                const icon = item.querySelector('.requirement-icon');
                const text = item.querySelector('.requirement-text');

                if (!icon || !text) {
                    console.error('Icon or text not found for requirement:', req);
                    return;
                }

                if (requirements[req]) {
                    icon.className = 'fas fa-check-circle requirement-icon text-success';
                    text.classList.add('text-success');
                    text.classList.remove('text-muted');
                    item.classList.add('requirement-met');
                } else {
                    icon.className = 'fas fa-times-circle requirement-icon text-danger';
                    text.classList.remove('text-success');
                    text.classList.add('text-muted');
                    item.classList.remove('requirement-met');
                }
            });

            return requirements;
        }

        // Calculate password strength
        function calculateStrength(password, requirements) {
            const metRequirements = Object.values(requirements).filter(Boolean).length;
            const strengthPercentage = (metRequirements / 5) * 100;

            console.log('Calculating strength:', metRequirements, 'out of 5 requirements met');

            let strengthText = 'Weak';
            let strengthClass = 'weak';

            if (strengthPercentage >= 100) {
                strengthText = 'Very Strong';
                strengthClass = 'very-strong';
            } else if (strengthPercentage >= 80) {
                strengthText = 'Strong';
                strengthClass = 'strong';
            } else if (strengthPercentage >= 60) {
                strengthText = 'Good';
                strengthClass = 'good';
            } else if (strengthPercentage >= 40) {
                strengthText = 'Fair';
                strengthClass = 'fair';
            }

            // Update strength meter
            if (strengthBar && strengthLevel) {
                strengthBar.style.width = strengthPercentage + '%';
                strengthBar.className = `strength-bar ${strengthClass}`;
                strengthLevel.textContent = strengthText;
                strengthLevel.className = `strength-level ${strengthClass}`;
                console.log('Strength updated:', strengthText, strengthPercentage + '%');
            } else {
                console.error('Strength bar or level element not found');
            }

            return strengthPercentage;
        }

        // Password input event listener
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                console.log('Password input changed:', password.length, 'characters');
                const requirements = validatePassword(password);
                calculateStrength(password, requirements);

                // Check password confirmation match
                checkPasswordMatch();
            });
            console.log('Password input event listener added');
        } else {
            console.error('Password input element not found');
        }

        // Password confirmation validation
        function checkPasswordMatch() {
            if (!passwordInput || !confirmPasswordInput) {
                console.error('Password inputs not found for matching');
                return;
            }

            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    if (matchIndicator) matchIndicator.style.display = 'block';
                    if (mismatchIndicator) mismatchIndicator.style.display = 'none';
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    console.log('Passwords match');
                } else {
                    if (matchIndicator) matchIndicator.style.display = 'none';
                    if (mismatchIndicator) mismatchIndicator.style.display = 'block';
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmPasswordInput.classList.remove('is-valid');
                    console.log('Passwords do not match');
                }
            } else {
                if (matchIndicator) matchIndicator.style.display = 'none';
                if (mismatchIndicator) mismatchIndicator.style.display = 'none';
                confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
            }
        }

        // Confirm password input event listener
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            console.log('Confirm password input event listener added');
        } else {
            console.error('Confirm password input element not found');
        }

        console.log('All password enhancement JavaScript initialized');
    });

    // Worker ID format helper function
    function updateWorkerIdSuggestion() {
        const departmentSelect = document.getElementById('department');
        const workerIdHelp = document.getElementById('worker-id-help');
        const workerIdFormat = document.getElementById('worker-id-format');

        if (departmentSelect.value) {
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            const prefix = selectedOption.getAttribute('data-prefix') || 'PW';

            workerIdFormat.textContent = `Expected format: ${prefix}XXX (e.g., ${prefix}001, ${prefix}002)`;
            workerIdHelp.style.display = 'block';
        } else {
            workerIdHelp.style.display = 'none';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateWorkerIdSuggestion();
    });
</script>
@endpush
