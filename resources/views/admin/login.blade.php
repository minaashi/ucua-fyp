@extends('layouts.auth')

@section('content')
<div class="auth-wrapper">
    <div class="split-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="brand-header">
                <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" height="45">
                <h4 class="ms-2 mb-0 text-white fw-bold">Admin Login</h4>
            </div>
            <div class="image-container">
                <img src="{{ asset('images/admin-auth.jpg') }}" alt="Admin Login" class="welcome-image">
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="auth-card">
                <h3 class="text-center fw-bold mb-4">Admin Login</h3>
                <p class="text-center text-muted mb-4">Enter the admin code to proceed</p>

                <!-- This form is for dummy login; no need for real data entry -->
                <form method="POST" action="#">
                    @csrf

                    <!-- Disabled Admin Code -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="admin_code" name="admin_code" placeholder="Admin Code" required disabled>
                        <label for="admin_code">Admin Login Code (Disabled)</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4" disabled>
                        Login as Admin (Dummy)
                    </button>

                    <!-- Redirect to real admin registration link -->
                    <div class="text-center">
                        <a href="{{ route('admin.register') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Go to Admin Registration
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: UCUA Team</p>
    </footer>
</div>
@endsection
