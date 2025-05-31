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
                <h3 class="text-center fw-bold mb-4">Verify Your Email Address</h3>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="text-center mb-4">A One-Time Password (OTP) has been sent to your email address. Please check your inbox and enter the OTP below to verify your account.</p>
                
                <div class="text-center">
                    <a href="{{ route('otp.form', ['email' => request()->email]) }}" class="btn btn-primary">Enter OTP</a>
                </div>

            </div>
        </div>
    </div>
    
    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy, Dr Cik.Feresa Binti Mohd Foozy</p>
    </footer>
</div>
@endsection 