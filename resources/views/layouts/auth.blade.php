<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

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
            flex: 1.2;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .right-panel {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f0f9ff;
        }

        .brand-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .image-container {
            width: 100%;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0rem;
        }

        .welcome-image {
            max-width: 90%;
            max-height: 60vh;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .auth-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: auto;
            padding: 10px;
            background: #ffffff;
            color: #6c757d;
            text-align: center;
            font-size: 14px;
            border-top: 1px solid #e4e7eb;
        }

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

            .auth-footer {
                font-size: 12px;
                padding: 8px;
            }
        }

        .form-floating {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 4;
            padding: 5px;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #4b5563;
        }

        .form-control {
            height: 60px;
            border-radius: 10px;
            border: 2px solid #e4e7eb;
            padding: 1rem 2.5rem 1rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('.password-toggle');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                // Toggle the password visibility
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle the icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html> 