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
                                    <p class="display-6 fw-bold">{{ $totalReports }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Reports</h5>
                                    <p class="display-6 fw-bold">{{ $pendingReports }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">Resolved Cases</h5>
                                    <p class="display-6 fw-bold">{{ $resolvedReports }}</p>
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
                                @forelse($recentReports as $report)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $report->non_compliance_type }} - {{ $report->location }}
                                    <span class="badge bg-{{ $report->status === 'pending' ? 'warning' : ($report->status === 'resolved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </li>
                                @empty
                                <li class="list-group-item text-center">No reports found</li>
                                @endforelse
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
