@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
@endphp


<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        .auth-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
        }

        .auth-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .form-floating {
            position: relative;
        }

        .form-control {
            height: 60px;
            border-radius: 10px;
            border: 2px solid #e4e7eb;
            padding: 1rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: none;
        }

        .form-floating label {
            padding: 1rem 1rem;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 4;
        }

        .btn-primary {
            border-radius: 10px;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
        }
    </style>

    <!-- Stack Styles -->
    @stack('styles')
</head>
<style>
    .auth-wrapper {
        min-height: 100vh;
        background: #f5f7fa;
        position: relative;
        padding-bottom: 60px;
    }

    .split-container {
        display: flex;
        min-height: calc(100vh - 60px);
    }

    .left-panel {
        flex: 1.2; /* Made slightly wider */
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); /* Changed to blue gradient */
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .right-panel {
        flex: 0.8; /* Made slightly narrower */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: #f0f9ff; /* Light blue background */
    }

    .brand-header {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem; /* Reduced margin to make more room for image */
    }

    .welcome-section {
        text-align: center;
        margin-bottom: 2rem; /* Reduced margin to make more room for image */
    }

    .image-container {
        width: 100%;
        flex-grow: 1; /* Takes up remaining space */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0rem;
    }

    .welcome-image {
        max-width: 90%; /* Increased from 80% */
        max-height: 60vh; /* Added max height */
        object-fit: cover; /* Maintains aspect ratio */
        border-radius: 20px; /* Increased border radius */
        box-shadow: 0 10px 30px rgba(0,0,0,0.2); /* Added shadow for depth */
    }

    .auth-card {
        width: 100%;
        max-width: 450px;
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .form-control {
        height: 55px;
        border-radius: 10px;
        border: 2px solid #e4e7eb;
    }

    .form-control:focus {
        border-color: #3b82f6; /* Changed to blue */
        box-shadow: none;
    }

    .btn-primary {
        height: 55px;
        border-radius: 10px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); /* Changed to blue gradient */
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3); /* Changed to blue shadow */
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 4;
    }

    .auth-footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: auto;
    padding: 10px;
    background: #ffffff; /* Background color for footer */
    color: #6c757d; /* Text color */
    text-align: center;
    font-size: 14px; /* Font size */
    border-top: 1px solid #e4e7eb; /* Optional border for separation */
}

@media (max-width: 768px) {
    .auth-footer {
        font-size: 12px; /* Slightly smaller text on mobile */
        padding: 8px; /* Reduced padding on smaller screens */
    }
}

    

    /* Added responsive adjustments */
    @media (max-width: 768px) {
        .split-container {
            flex-direction: column;
        }

        .left-panel {
            padding: 1.5rem;
        }

        .image-container {
            padding: 1rem 0;
        }

        .welcome-image {
            max-width: 95%;
            max-height: 40vh;
        }

        .auth-card {
            padding: 1.5rem;
        }
    }
</style>

    @media (max-width: 768px) {
        .split-container {
            flex-direction: column;
        }

        .left-panel {
            padding: 1.5rem;
        }

        .welcome-image {
            display: none;
        }

        .auth-card {
            padding: 1.5rem;
        }
    }
</style>
<body>
    <div id="app">
        @if(!request()->routeIs('login') && !request()->routeIs('register'))
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @endif

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.password-toggle');
            
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>

    <!-- Stack Scripts -->
    @stack('scripts')

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @yield('content')
    </div>
</body>
</html>
</body>
</html>

