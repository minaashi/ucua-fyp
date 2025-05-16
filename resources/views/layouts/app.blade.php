<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tailwind-like utility classes (for the sidebar design) -->
    <style>
        .min-h-screen { min-height: 100vh; }
        .font-sans { font-family: Inter, sans-serif; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .divide-y > :not([hidden]) ~ :not([hidden]) { border-top-width: 1px; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
        .hover\:bg-blue-50:hover { background-color: #eff6ff; }
        .hover\:text-blue-600:hover { color: #2563eb; }
    </style>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    @if(auth()->check() && auth()->user()->isAdmin())
        <!-- Admin Layout with Sidebar -->
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-md" style="min-width: 16rem;">
                <div class="p-4 border-b">
                    <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
                    <h2 class="text-xl font-bold text-center text-gray-800 mt-2">UCUA Admin Dashboard</h2>
                </div>

                <nav class="mt-6">
                    <ul class="list-unstyled pl-3 pr-3">
                        <li class="mb-2">
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center px-4 py-2 rounded 
                                      {{ Request::routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} 
                                      hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-line w-5 mr-3"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('admin.reports.index') }}" 
                               class="flex items-center px-4 py-2 rounded 
                                      {{ Request::routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} 
                                      hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-file-alt w-5 mr-3"></i>
                                <span>Manage Reports</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('admin.settings') }}" 
                               class="flex items-center px-4 py-2 rounded 
                                      {{ Request::routeIs('admin.settings') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} 
                                      hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-cog w-5 mr-3"></i>
                                <span>Admin Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <div class="flex-1 flex flex-column">
                <!-- Header -->
                <header class="bg-primary text-white p-4 shadow-md">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="text-2xl font-bold m-0">@yield('page-title', 'UCUA Admin')</h1>
                        <div class="d-flex align-items-center">
                            <span class="mr-3">Welcome, {{ auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-grow-1 p-4 bg-light">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <!-- Regular Layout (for non-admin pages) -->
        <div class="min-h-screen bg-gray-100">
            @yield('content')
        </div>
    @endif

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>