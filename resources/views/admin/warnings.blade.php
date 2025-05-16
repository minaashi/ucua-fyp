@extends('layouts.auth')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            @include('admin.partials.sidebar')

            <main class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
                <div class="content-wrapper px-md-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Warning Letters Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendWarningModal">
                                <i class="fas fa-envelope me-1"></i> Send New Warning
                            </button>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Warning Letters Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title text-muted">Total Warnings</h6>
                                            <h2 class="mb-0">{{ $totalWarnings }}</h2>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title text-muted">Pending</h6>
                                            <h2 class="mb-0">{{ $pendingWarnings }}</h2>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle p-3">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Letters Table -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Warning Letters</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Report Reference</th>
                                            <th>Status</th>
                                            <th>Date Sent</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($warnings as $warning)
                                            <tr>
                                                <td>WL-{{ str_pad($warning->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $warning->user->name }}</td>
                                                <td>RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $warning->status == 'pending' ? 'warning' : ($warning->status == 'sent' ? 'info' : 'success') }}">
                                                        {{ ucfirst($warning->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $warning->sent_at ? $warning->sent_at->format('M d, Y') : 'Not sent' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewWarningModal{{ $warning->id }}">
                                                        View
                                                    </button>
                                                    @if($warning->status != 'sent')
                                                        <form action="{{ route('admin.warnings.resend', $warning) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning">Resend</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- View Warning Modal -->
                                            <div class="modal fade" id="viewWarningModal{{ $warning->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Warning Letter Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <strong>Warning ID:</strong> WL-{{ str_pad($warning->id, 4, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>User:</strong> {{ $warning->user->name }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Report Reference:</strong> RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Status:</strong>
                                                                <span class="badge bg-{{ $warning->status == 'pending' ? 'warning' : ($warning->status == 'sent' ? 'info' : 'success') }}">
                                                                    {{ ucfirst($warning->status) }}
                                                                </span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Message:</strong>
                                                                <p class="mt-2">{{ $warning->message }}</p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Date Sent:</strong> {{ $warning->sent_at ? $warning->sent_at->format('M d, Y H:i') : 'Not sent' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No warning letters found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $warnings->links() }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    @include('admin.partials.footer')
</div>

<!-- Send Warning Modal -->
<div class="modal fade" id="sendWarningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Warning Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.warnings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report_id" class="form-label">Select Report</label>
                        <select class="form-select" id="report_id" name="report_id" required>
                            <option value="">Select a report</option>
                            @foreach(App\Models\Report::where('category', 'unsafe_act')->get() as $report)
                                <option value="{{ $report->id }}">
                                    RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }} - {{ $report->non_compliance_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Warning Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Warning</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 