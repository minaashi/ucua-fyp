@extends('layouts.auth')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            <!-- Include the sidebar partial -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
                <div class="content-wrapper px-md-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Admin Dashboard</h1>
                        <div>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Logout</button>
                            </form>
                        </div>
                    </div>

                    <!-- Dashboard Statistics -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">Total Reports</h5>
                                    <p class="display-6 fw-bold">{{ rand(50, 200) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Reports</h5>
                                    <p class="display-6 fw-bold">{{ rand(10, 50) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">Resolved Cases</h5>
                                    <p class="display-6 fw-bold">{{ rand(30, 150) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Overview -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Recent Reports</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Unsafe Condition Report #{{ rand(1000, 9999) }}
                                    <span class="badge bg-warning">Pending</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Equipment Malfunction #{{ rand(1000, 9999) }}
                                    <span class="badge bg-success">Resolved</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Hazardous Material Leak #{{ rand(1000, 9999) }}
                                    <span class="badge bg-danger">Critical</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Manage Reports</h5>
                                    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">Go to Reports</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Admin Settings</h5>
                                    <a href="{{ route('admin.settings') }}" class="btn btn-secondary">Settings</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    @include('admin.partials.footer')
</div>
@endsection
