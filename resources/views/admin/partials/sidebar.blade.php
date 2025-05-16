<nav class="col-md-3 col-lg-2 px-0 bg-primary position-fixed sidebar">
    <div class="position-sticky">
        <div class="text-center py-4">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" height="45">
            <h5 class="text-white mt-2">Admin Panel</h5>
        </div>
        
        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.dashboard.dummy') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.reports.dummy') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-file-alt me-2"></i>
                    Report Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.users.dummy') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i>
                    User Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.warnings.dummy') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.warnings') }}">
                    <i class="fas fa-envelope me-2"></i>
                    Warning Letters
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.settings.dummy') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.settings') }}">
                    <i class="fas fa-cog me-2"></i>
                    Admin Settings
                </a>
            </li>
            
        </ul>
    </div>
</nav>

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