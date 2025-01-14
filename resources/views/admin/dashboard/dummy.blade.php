@extends('layouts.auth')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="row">
            @include('admin.partials.sidebar')

            <main class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
                <div class="content-wrapper px-md-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                        <h1 class="h2">Admin Dashboard</h1>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row g-3 mb-4">
                        <!-- Total Reports -->
                        <div class="col-md-3">
                            <div class="card border-primary h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted">Total Reports</div>
                                        <h2 class="mb-0">150</h2>
                                    </div>
                                    <div class="bg-primary text-white rounded-circle p-3">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Users -->
                        <div class="col-md-3">
                            <div class="card border-success h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted">Active Users</div>
                                        <h2 class="mb-0">45</h2>
                                    </div>
                                    <div class="bg-success text-white rounded-circle p-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Reports -->
                        <div class="col-md-3">
                            <div class="card border-warning h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted">Pending Reports</div>
                                        <h2 class="mb-0">12</h2>
                                    </div>
                                    <div class="bg-warning text-white rounded-circle p-3">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Letters -->
                        <div class="col-md-3">
                            <div class="card border-info h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted">Warning Letters</div>
                                        <h2 class="mb-0">8</h2>
                                    </div>
                                    <div class="bg-info text-white rounded-circle p-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Reports Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Recent Reports</h5>
                                <a href="#" class="btn btn-link">View All</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Reporter</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#001</td>
                                            <td>Safety Violation Report</td>
                                            <td>John Doe</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                            <td>2024-02-20</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <button class="btn btn-sm btn-warning">Send Warning</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#002</td>
                                            <td>Equipment Maintenance</td>
                                            <td>Jane Smith</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>2024-02-19</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <button class="btn btn-sm btn-warning">Send Warning</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                            </div>
                @include('admin.partials.footer')
            </main>
                            </div>
                        </div>
</div>

<style>
.main-content {
    margin-left: 250px;
    padding-bottom: 70px;
}

.content-wrapper {
    padding-top: 1rem;
}

.card {
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.rounded-circle {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table th {
    font-weight: 600;
    color: #444;
}

.badge {
    padding: 0.5em 0.8em;
}
</style>
@endsection 