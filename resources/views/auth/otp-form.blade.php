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
                <h3 class="text-center fw-bold mb-4">Enter OTP</h3>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf

                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                    <div class="form-floating mb-3">
                        <input id="otp" type="text" 
                            class="form-control @error('otp') is-invalid @enderror" 
                            name="otp" required autofocus>
                        <label for="otp">One-Time Password (OTP)</label>
                        @error('otp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">Verify OTP</button>
                </form>

                <div class="text-center mb-0">
                    <p class="mb-2">Didn't receive the OTP?</p>
                    <form method="POST" action="{{ route('otp.resend') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                        <button type="submit" class="btn btn-link text-primary text-decoration-none fw-bold p-0">
                            Resend OTP
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    
    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy</p>
    </footer>
</div>
@endsection 