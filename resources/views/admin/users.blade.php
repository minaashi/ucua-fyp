@extends('layouts.auth')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            @include('admin.partials.sidebar')

            <main class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
                <div class="content-wrapper px-md-4">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-4" id="managementTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                                <i class="fas fa-users me-1"></i> Users
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="departments-tab" data-bs-toggle="tab" data-bs-target="#departments" type="button" role="tab">
                                <i class="fas fa-building me-1"></i> Departments
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="managementTabsContent">
                        <!-- Users Tab -->
                        <div class="tab-pane fade show active" id="users" role="tabpanel">
                            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                                <h1 class="h2">User Management</h1>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    <i class="fas fa-user-plus me-1"></i> Add New User
                                </button>
                            </div>

                            <!-- User Stats -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Users</h5>
                                            <p class="display-6 fw-bold">{{ $totalUsers }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <h5 class="card-title">Admin Users</h5>
                                            <p class="display-6 fw-bold">{{ $adminUsers }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <h5 class="card-title">Port Workers</h5>
                                            <p class="display-6 fw-bold">{{ $portWorkers }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Search and Filter -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <form action="{{ route('admin.users.index') }}" method="GET">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <select name="role" class="form-select">
                                                    <option value="All Roles" {{ request('role') == 'All Roles' ? 'selected' : '' }}>All Roles</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Users Table -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Department</th>
                                                    <th>Role</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($users as $user)
                                                    <tr>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->department ?? 'Not Assigned' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $user->hasRole('admin') ? 'primary' : 'secondary' }}">
                                                                {{ ucfirst(str_replace('_', ' ', $user->roles->first()->name ?? 'port_worker')) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <!-- Edit User Modal -->
                                                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit User</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="name" class="form-label">Name</label>
                                                                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="email" class="form-label">Email</label>
                                                                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="department" class="form-label">Department</label>
                                                                            <select class="form-select" id="department" name="department">
                                                                                <option value="">Select Department</option>
                                                                                @foreach($departments as $department)
                                                                                    <option value="{{ $department->name }}" {{ $user->department == $department->name ? 'selected' : '' }}>
                                                                                        {{ $department->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="role" class="form-label">Role</label>
                                                                            <select class="form-select" id="role" name="role" required>
                                                                                @foreach($roles as $role)
                                                                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary">Update User</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No users found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Departments Tab -->
                        <div class="tab-pane fade" id="departments" role="tabpanel">
                            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                                <h1 class="h2">Department Management</h1>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                                    <i class="fas fa-plus me-1"></i> Add New Department
                                </button>
                            </div>

                            <!-- Departments Table -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Department Name</th>
                                                    <th>Email</th>
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
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $department->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this department?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <!-- Edit Department Modal -->
                                                    <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Department</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="name" class="form-label">Department Name</label>
                                                                            <input type="text" class="form-control" id="name" name="name" value="{{ $department->name }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="email" class="form-label">Department Email</label>
                                                                            <input type="email" class="form-control" id="email" name="email" value="{{ $department->email }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="head_name" class="form-label">Head of Department Name</label>
                                                                            <input type="text" class="form-control" id="head_name" name="head_name" value="{{ $department->head_name }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="head_email" class="form-label">Head of Department Email</label>
                                                                            <input type="email" class="form-control" id="head_email" name="head_email" value="{{ $department->head_email }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="head_phone" class="form-label">Head of Department Phone</label>
                                                                            <input type="tel" class="form-control" id="head_phone" name="head_phone" value="{{ $department->head_phone }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="is_active" class="form-label">Status</label>
                                                                            <select class="form-select" id="is_active" name="is_active">
                                                                                <option value="1" {{ $department->is_active ? 'selected' : '' }}>Active</option>
                                                                                <option value="0" {{ !$department->is_active ? 'selected' : '' }}>Inactive</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary">Update Department</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
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
                    </div>
                </div>
            </main>
        </div>
    </div>
    @include('admin.partials.footer')
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->name }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Department Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="head_name" class="form-label">Head of Department Name</label>
                        <input type="text" class="form-control" id="head_name" name="head_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="head_email" class="form-label">Head of Department Email</label>
                        <input type="email" class="form-control" id="head_email" name="head_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="head_phone" class="form-label">Head of Department Phone</label>
                        <input type="tel" class="form-control" id="head_phone" name="head_phone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 