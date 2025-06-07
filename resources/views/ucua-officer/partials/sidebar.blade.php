<aside class="w-64 bg-white shadow-md">
    <div class="p-4 border-b">
        <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
        <h2 class="text-xl font-bold text-center text-gray-800 mt-2">UCUA Officer Dashboard</h2>
    </div>

    <nav class="mt-6">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('ucua.dashboard') }}" 
                   class="flex items-center px-4 py-2 {{ Request::routeIs('ucua.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Report Overview</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ucua.assign-departments-page') }}" 
                   class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-tasks w-5"></i>
                    <span>Assign Departments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ucua.warnings') }}" 
                   class="flex items-center px-4 py-2 text-gray-600 hover:bg-yellow-100 hover:text-yellow-800 transition-colors duration-200">
                    <i class="fas fa-exclamation-triangle w-5 animate-pulse text-yellow-500"></i>
                    <span class="ml-2 font-semibold">Warning Letters</span>
                    <span class="ml-2 bg-yellow-200 text-yellow-800 text-xs px-2 py-1 rounded-full">NEW</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ucua.reminders') }}"
                   class="flex items-center px-4 py-2 text-gray-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200">
                    <i class="fas fa-bell w-5 animate-bounce text-red-500"></i>
                    <span class="ml-2 font-semibold">Reminders</span>
                    @php
                        $urgentCount = \App\Models\Report::where('status', '!=', 'resolved')
                            ->whereNotNull('deadline')
                            ->where('deadline', '<=', now()->addDays(3))
                            ->count();
                    @endphp
                    @if($urgentCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $urgentCount }}</span>
                    @else
                        <span class="ml-2 bg-green-200 text-green-700 text-xs px-2 py-1 rounded-full">OK</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('help.ucua') }}"
                   class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-question-circle w-5"></i>
                    <span class="ml-2">Help</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>