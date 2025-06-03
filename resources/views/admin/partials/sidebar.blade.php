<aside class="w-64 bg-white text-gray-900 flex flex-col border-r border-gray-200">
    <div class="p-6 flex flex-col items-center border-b border-gray-200">
        <img src="{{ asset('images/ucua-logo.png') }}" alt="JohorPort Logo" class="h-12 mb-2">
        <span class="font-bold text-lg">Admin Panel</span>
    </div>
    <nav class="flex-1 mt-6">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('admin.reports.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-file-alt w-5"></i>
                    <span>Report Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('admin.users.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-users w-5"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.departments.index') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('admin.departments.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-building w-5"></i>
                    <span>Departments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.warnings.index') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('admin.warnings.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-envelope w-5"></i>
                    <span>Warning Letters</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('admin.settings') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-cog w-5"></i>
                    <span>Admin Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('help.admin') }}" class="flex items-center px-4 py-2 {{ Request::routeIs('help.admin') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                    <i class="fas fa-question-circle w-5"></i>
                    <span>Help</span>
                </a>
            </li>
            <li class="mt-4">
                <form method="POST" action="{{ route('logout') }}" class="px-6">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 bg-gray-100 hover:bg-red-200 rounded transition font-medium">
                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<style>
.sidebar {
    height: 100vh; /* Ensures it spans full screen height */
    width: 250px;
    z-index: 1030;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Ensures full height usage */
}

.position-sticky {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.nav{
    flex-grow: 1;
}
.nav-link {
    cursor: pointer;
    transition: all 0.3s;
    padding: 0.8rem 1rem;
    margin-bottom: 0.2rem;
    border-radius: 0.25rem;
    display: block;
    text-decoration: none;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    font-weight: bold;
}

.nav-item {
    margin: 0.2rem 0;
}

.nav-link i {
    width: 20px;
    text-align: center;
}
</style> 