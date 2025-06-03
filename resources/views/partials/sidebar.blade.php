<aside class="w-64 bg-white shadow-md">
    <div class="p-4 border-b">
        <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
        <h2 class="text-xl font-bold text-center text-gray-800 mt-2">Reporting System UCUA</h2>
    </div>
    <nav class="mt-6">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-2 {{ Request::routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Report Overview</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.create') }}" 
                   class="flex items-center px-4 py-2 {{ Request::routeIs('reports.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-file-alt w-5"></i>
                    <span>Submit Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.track') }}" 
                   class="flex items-center px-4 py-2 {{ Request::routeIs('reports.track') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-search w-5"></i>
                    <span>Track Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('profile.show') }}"
                   class="flex items-center px-4 py-2 {{ Request::routeIs('profile.show') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('help.user') }}"
                   class="flex items-center px-4 py-2 {{ Request::routeIs('help.user') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-question-circle w-5"></i>
                    <span>Help</span>
                </a>
            </li>
            <!-- Logout Button -->
            <li>
                <form method="POST" action="{{ route('logout') }}" class="px-4">
                    @csrf
                    <button type="submit" 
                            class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside> 