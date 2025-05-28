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
    <a href="#" id="howItWorksBtn" class="text-gray-600 hover:text-blue-700">How It Works</a>
    <a href="#" id="supportBtn" class="text-gray-600 hover:text-blue-700">Support</a>
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
    <!-- How It Works Modal -->
    <div id="howItWorksModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
      <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-8 relative animate-fade-in">
        <button id="closeHowItWorks" class="absolute top-2 right-2 text-gray-400 hover:text-blue-600 text-2xl">&times;</button>
        <h2 class="text-2xl font-bold mb-4 text-blue-700 flex items-center"><i class="fas fa-lightbulb mr-2 text-yellow-400"></i>How It Works</h2>
        <ol class="list-decimal list-inside text-gray-700 space-y-2">
          <li><span class="font-semibold">Spot an Issue:</span> Notice any unsafe act or condition at the port? Don't hesitate!</li>
          <li><span class="font-semibold">Report Easily:</span> Click <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Sign Up</span> or <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Login</span> to start your report.</li>
          <li><span class="font-semibold">Track Progress:</span> Get real-time updates as your report is reviewed and resolved.</li>
          <li><span class="font-semibold">Celebrate Safety:</span> Help us build a safer, happier port community—your action matters!</li>
        </ol>
        <div class="mt-6 text-center">
          <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-full font-semibold"><i class="fas fa-check-circle mr-2"></i>Reporting saves lives!</span>
        </div>
      </div>
    </div>
    <!-- Support Modal -->
    <div id="supportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
      <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-8 relative animate-fade-in">
        <button id="closeSupport" class="absolute top-2 right-2 text-gray-400 hover:text-blue-600 text-2xl">&times;</button>
        <h2 class="text-2xl font-bold mb-4 text-blue-700 flex items-center"><i class="fas fa-hands-helping mr-2 text-blue-400"></i>Support</h2>
        <div class="text-gray-700 space-y-3">
          <p>Need help? Our support team is here for you 24/7!</p>
          <ul class="list-disc list-inside">
            <li>Email: <a href="mailto:support@johorport.com" class="text-blue-600 underline">jpb@johorport.com</a></li>
            <li>Phone: <span class="font-semibold">+60 7-253 5888</span></li>
            <li>Live Chat: <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Coming Soon!</span></li>
          </ul>
          
        </div>
      </div>
    </div>
    <script>
    // Modal logic
    const howItWorksBtn = document.getElementById('howItWorksBtn');
    const supportBtn = document.getElementById('supportBtn');
    const howItWorksModal = document.getElementById('howItWorksModal');
    const supportModal = document.getElementById('supportModal');
    const closeHowItWorks = document.getElementById('closeHowItWorks');
    const closeSupport = document.getElementById('closeSupport');

    howItWorksBtn.onclick = () => howItWorksModal.classList.remove('hidden');
    supportBtn.onclick = () => supportModal.classList.remove('hidden');
    closeHowItWorks.onclick = () => howItWorksModal.classList.add('hidden');
    closeSupport.onclick = () => supportModal.classList.add('hidden');
    window.onclick = function(event) {
      if (event.target === howItWorksModal) howItWorksModal.classList.add('hidden');
      if (event.target === supportModal) supportModal.classList.add('hidden');
    }
    </script>
    <style>
    @keyframes fade-in {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
      animation: fade-in 0.2s ease;
    }
    </style>
</body>

</html>
