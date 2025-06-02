<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    @vite('resources/js/app.js')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100">
<div id="app" class="min-h-screen flex">
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-blue-600 text-white flex flex-col min-h-screen transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="p-4 lg:p-6 flex flex-col items-center border-b border-blue-700">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="JohorPort Logo" class="h-10 lg:h-12 mb-2">
            <span class="font-bold text-base lg:text-lg">Admin Panel</span>
        </div>
        <nav class="flex-1 mt-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 rounded transition {{ Request::routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 rounded transition {{ Request::routeIs('admin.reports.index') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-file-alt w-5 mr-3"></i>
                        Report Management
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 rounded transition {{ Request::routeIs('admin.users.index') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-users w-5 mr-3"></i>
                        User Management
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.warnings.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 rounded transition {{ Request::routeIs('admin.warnings.index') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-envelope w-5 mr-3"></i>
                        Warning Letters
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 rounded transition {{ Request::routeIs('admin.settings') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-cog w-5 mr-3"></i>
                        Admin Settings
                    </a>
                </li>
                <li class="mt-4">
                    <form method="POST" action="{{ route('logout') }}" class="px-6">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 bg-white hover:bg-red-50 hover:text-red-700 rounded transition">
                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
        <footer class="bg-blue-800 text-white p-4 mt-auto">
            <p class="text-center text-xs sm:text-sm">Copyright © 2025 Nursyahmina Mosdy, Dr Cik.Feresa Mohd Foozy</p>
        </footer>
    </div>
</div>

<!-- jQuery and Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Responsive Sidebar Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Toggle sidebar on mobile
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
    }

    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    });

    // Close sidebar on window resize if large screen
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }
    });
});
</script>

@stack('scripts')
</body>
</html> 