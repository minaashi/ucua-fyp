@extends('layouts.auth')

@php
use Illuminate\Support\Facades\Route;
@endphp

@section('content')
<div class="auth-wrapper user-auth">
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
                <h3 class="text-center fw-bold mb-4">Sign In</h3>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

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

                    <div class="form-floating mb-3 position-relative">
                        <input id="password" type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <i class="fas fa-eye password-toggle" aria-hidden="true"></i>
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

                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4">Sign In</button>

                    <div class="text-center">
                        <p class="mb-2">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">Create Account</a>
                        </p>
                        <div class="border-t border-gray-200 my-3"></div>
                        <p class="text-sm text-gray-600">Are you an UCUA officer?</p>
                        <a href="{{ route('ucua.login') }}" class="btn btn-outline-primary">
                            PIC Login <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    
                </form>
            </div>
        </div>
    </div>
    
    <footer class="auth-footer">
        <p class="mb-0">&copy; {{ date('Y') }} Copyright: Nursyahmina Mosdy, Dr Cik Feresa Mohd Foozy</p>
    </footer>
</div>
@endsection
