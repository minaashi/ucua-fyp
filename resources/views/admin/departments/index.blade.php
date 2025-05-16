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
                        <h1 class="h2">Department Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add New Department
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Departments Table -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Departments List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Department Name</th>
                                            <th>Department Email</th>
                                            <th>Head of Department</th>
                                            <th>Active Reports</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($departments as $department)
                                            <tr>
                                                <td>{{ $department->name }}</td>
                                                <td>{{ $department->email }}</td>
                                                <td>
                                                    {{ $department->head_name }}<br>
                                                    <small class="text-muted">{{ $department->head_email }}</small>
                                                </td>
                                                <td>{{ $department->reports_count }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">
                                                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.departments.edit', $department) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.departments.destroy', $department) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No departments found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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