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
                        <h1 class="h2">Report Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
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

                    <!-- Report Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="{{ route('admin.reports.index') }}" method="GET">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <select name="category" class="form-select">
                                            <option value="All Categories" {{ request('category') == 'All Categories' ? 'selected' : '' }}>All Categories</option>
                                            <option value="unsafe_act" {{ request('category') == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                                            <option value="unsafe_condition" {{ request('category') == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="All Status" {{ request('status') == 'All Status' ? 'selected' : '' }}>All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control" placeholder="Search reports..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Reports Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Report ID</th>
                                            <th>Type</th>
                                            <th>Location</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reports as $report)
                                            <tr>
                                                <td>RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $report->non_compliance_type }}</td>
                                                <td>{{ $report->location }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $report->category == 'unsafe_act' ? 'danger' : 'warning' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $report->category)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $report->status == 'pending' ? 'warning' : ($report->status == 'resolved' ? 'success' : 'info') }}">
                                                        {{ ucfirst($report->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewReportModal{{ $report->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $report->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this report?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- View Report Modal -->
                                            <div class="modal fade" id="viewReportModal{{ $report->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Report Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <strong>Report ID:</strong> RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Type:</strong> {{ $report->non_compliance_type }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Location:</strong> {{ $report->location }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Description:</strong> {{ $report->description }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Status:</strong> 
                                                                <span class="badge bg-{{ $report->status == 'pending' ? 'warning' : ($report->status == 'resolved' ? 'success' : 'info') }}">
                                                                    {{ ucfirst($report->status) }}
                                                                </span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Category:</strong> 
                                                                <span class="badge bg-{{ $report->category == 'unsafe_act' ? 'danger' : 'warning' }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $report->category)) }}
                                                                </span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Reported By:</strong> {{ $report->user->name }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Date:</strong> {{ $report->created_at->format('M d, Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Update Status Modal -->
                                            <div class="modal fade" id="updateStatusModal{{ $report->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Report Status</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label">Status</label>
                                                                    <select name="status" id="status" class="form-select" required>
                                                                        <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                        <option value="review" {{ $report->status == 'review' ? 'selected' : '' }}>Review</option>
                                                                        <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="category" class="form-label">Category</label>
                                                                    <select name="category" id="category" class="form-select" required>
                                                                        <option value="unsafe_act" {{ $report->category == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                                                                        <option value="unsafe_condition" {{ $report->category == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No reports found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $reports->links() }}
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