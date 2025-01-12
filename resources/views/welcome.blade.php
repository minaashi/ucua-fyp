<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - UCUA Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-md py-4 px-8 flex justify-between items-center">
        <!-- Logo and Title -->
        <div class="flex items-center">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-10 mr-2">
            <span class="text-lg font-bold text-blue-700">UCUA Reporting System</span>
        </div>

       <!-- Navigation Links -->
<div class="space-x-4">
    <a href="#" class="text-gray-600 hover:text-blue-700">How It Works</a> <!-- Link will not work until page is created -->
    <a href="#" class="text-gray-600 hover:text-blue-700">Support</a> <!-- Link will not work until page is created -->
    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-700">Login</a>
    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Sign Up</a>
</div>

    </nav>

    <!-- Hero Section -->
    <header class="bg-cover bg-center py-32" style="background-image: url('{{ asset('images/ucua-illustration.png') }}');">
        <div class="text-center text-white max-w-3xl mx-auto">
            <h1 class="text-5xl font-bold leading-tight mb-4">Keep Your Workplace Safe</h1>
            <p class="text-xl mb-6">Easy way to report unsafe conditions and acts with the UCUA Reporting System.</p>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Committed to Safety at Johor Port</h2>
            <p class="text-lg text-gray-600">Quick way of reporting to maintain a safer port for everyone.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-4 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="text-center">
                <div class="mb-4 text-orange-500 text-6xl"><i class="fas fa-exclamation-triangle"></i></div>
                <h3 class="text-xl font-bold mb-2">Report Unsafe Acts</h3>
                <p class="text-gray-600">Quickly log and submit reports of safety violations at work.</p>
            </div>
            <!-- Feature 2 -->
            <div class="text-center">
                <div class="mb-4 text-blue-500 text-6xl"><i class="fas fa-chart-line"></i></div>
                <h3 class="text-xl font-bold mb-2">Track Report Status</h3>
                <p class="text-gray-600">Stay informed with real-time updates on report statuses.</p>
            </div>
            <!-- Feature 3 -->
            <div class="text-center">
                <div class="mb-4 text-green-500 text-6xl"><i class="fas fa-user-friends"></i></div>
                <h3 class="text-xl font-bold mb-2">User-Friendly Design</h3>
                <p class="text-gray-600">Intuitive and easy-to-use platform for everyone.</p>
            </div>
        </div>
    </section>
</body>

</html>
