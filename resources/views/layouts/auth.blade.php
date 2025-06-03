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
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 0;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Default background for admin pages */
        .admin-auth .left-panel {
            background-image: url('/images/admin-auth.jpg');
        }

        /* Background for user login page */
        .user-auth .left-panel {
            background-image: url('/images/auth-image.jpg');
        }

        /* Background for registration page - real image without overlay */
        .register-auth .left-panel {
            background-image: url('/images/auth-image.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* Left Panel Content */
        .left-panel-content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }

        /* Welcome Container Box */
        .welcome-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            max-width: 450px;
            width: 100%;
        }

        .welcome-text {
            text-align: center;
        }

        .welcome-text h2 {
            font-size: 2.2rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            line-height: 1.2;
        }

        .welcome-text p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            font-weight: 500;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .feature-item i {
            font-size: 1.2rem;
            color: #10b981;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        /* Background for UCUA pages */
        .ucua-auth .left-panel {
            background-image: url('/images/admin-auth.jpg');
        }

        /* Background for department pages */
        .department-auth .left-panel {
            background-image: url('/images/register.png');
        }

        .right-panel {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0f4ff 100%);
            position: relative;
        }

        /* Creative floating elements for registration page */
        .register-auth .right-panel::before {
            content: '';
            position: absolute;
            top: 10%;
            right: 10%;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .register-auth .right-panel::after {
            content: '';
            position: absolute;
            bottom: 15%;
            left: 5%;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .brand-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .logo-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 15px;
            border-radius: 8px;
            display: inline-block;
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }



        .auth-card {
            width: 100%;
            max-width: 550px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 3rem;
            border-radius: 24px;
            box-shadow:
                0 20px 40px rgba(0,0,0,0.1),
                0 0 0 1px rgba(255,255,255,0.2);
            min-height: 600px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Enhanced auth card for registration */
        .register-auth .auth-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            box-shadow:
                0 25px 50px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,255,255,0.3),
                inset 0 1px 0 rgba(255,255,255,0.4);
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



        .form-control {
            height: 60px;
            border-radius: 12px;
            border: 2px solid rgba(228, 231, 235, 0.8);
            padding: 1rem 2.5rem 1rem 1rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow:
                0 0 0 3px rgba(59, 130, 246, 0.1),
                0 4px 12px rgba(59, 130, 246, 0.15);
            background: rgba(255, 255, 255, 1);
            transform: translateY(-1px);
        }

        .form-control:hover {
            border-color: rgba(59, 130, 246, 0.5);
            transform: translateY(-0.5px);
        }

        /* Enhanced form floating labels */
        .form-floating > label {
            color: #6b7280;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #3b82f6;
            font-weight: 600;
        }

        /* Enhanced Password Policy Styles */
        .password-section {
            margin-bottom: 1.5rem;
        }

        .password-strength-container {
            margin-top: 0.75rem;
        }

        .password-strength-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .strength-text {
            color: #6c757d;
            font-weight: 500;
        }

        .strength-level {
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .strength-level.weak { color: #dc3545; }
        .strength-level.fair { color: #fd7e14; }
        .strength-level.good { color: #ffc107; }
        .strength-level.strong { color: #20c997; }
        .strength-level.very-strong { color: #28a745; }

        .password-strength-meter {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
            border-radius: 4px;
            background: linear-gradient(90deg, #dc3545, #e74c3c);
        }

        .strength-bar.weak { background: linear-gradient(90deg, #dc3545, #e74c3c); }
        .strength-bar.fair { background: linear-gradient(90deg, #fd7e14, #f39c12); }
        .strength-bar.good { background: linear-gradient(90deg, #ffc107, #f1c40f); }
        .strength-bar.strong { background: linear-gradient(90deg, #20c997, #1abc9c); }
        .strength-bar.very-strong { background: linear-gradient(90deg, #28a745, #27ae60); }

        .password-requirements-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 0.75rem;
        }

        .requirements-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
        }

        .requirements-header i {
            color: #6c757d;
        }

        .requirements-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            padding: 0.25rem 0;
        }

        .requirement-icon {
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .requirement-text {
            font-size: 0.875rem;
            transition: color 0.3s ease;
            color: #6c757d;
        }

        .requirement-item.requirement-met {
            transform: translateX(2px);
        }

        .requirement-item.requirement-met .requirement-text {
            font-weight: 500;
        }

        /* Password match indicators */
        .password-match-indicator,
        .password-mismatch-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            padding: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .password-match-indicator i,
        .password-mismatch-indicator i {
            font-size: 1rem;
        }

        /* Enhanced password toggle styling */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 4;
            padding: 8px;
            transition: all 0.2s ease;
            border-radius: 4px;
        }

        .password-toggle:hover {
            color: #495057;
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Form validation enhancements */
        .form-control.is-valid {
            border-color: #28a745;
            background-image: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: none;
        }

        /* Creative Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes checkmark {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .requirement-item.requirement-met .requirement-icon {
            animation: checkmark 0.3s ease;
        }





        /* Enhanced Submit Button */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .register-auth .left-panel {
                background-attachment: scroll;
            }

            .register-auth .right-panel::before,
            .register-auth .right-panel::after {
                display: none;
            }

            .auth-card {
                padding: 2rem;
                margin: 1rem;
                border-radius: 16px;
            }

            .password-requirements-card {
                padding: 0.75rem;
            }

            .requirements-header,
            .requirement-text {
                font-size: 0.8rem;
            }

            .password-strength-label {
                font-size: 0.8rem;
            }

            .left-panel-content {
                padding: 1.5rem;
            }

            .welcome-container {
                padding: 2rem;
                border-radius: 16px;
                max-width: 100%;
            }

            .welcome-text h2 {
                font-size: 1.8rem;
            }

            .welcome-text p {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }

            .feature-item {
                padding: 0.5rem 0.75rem;
            }

            .feature-item i {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html> 