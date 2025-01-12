@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <header class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" style="height: 50px; margin-right: 10px;">
            <h2>Port Worker Dashboard</h2>
        </div>
        <div>
            <p>Welcome, {{ auth()->user()->name }}</p>
        </div>
    </header>

    <div class="row">
        <!-- Sidebar Section -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <ul class="nav flex-column">
                <!-- Dashboard Link -->
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('home') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <!-- Submit Report -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.create') }}">
                        <i class="fas fa-plus-circle"></i> Submit Report
                    </a>
                </li>

                <!-- Report History -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="fas fa-history"></i> Report History
                    </a>
                </li>

                <!-- Track Status -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.status') }}">
                        <i class="fas fa-search"></i> Track Status
                    </a>
                </li>

                <!-- Settings (Empty for now) -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-cogs"></i> Settings
                    </a>
                </li>

                <!-- Help Section (Can redirect to help page in the future) -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-question-circle"></i> Help
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content Section -->
        <main class="col-md-9 col-lg-10 px-md-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Reports</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $totalReports }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Pending Reports</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $pendingReports }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Solved Reports</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $solvedReports }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
