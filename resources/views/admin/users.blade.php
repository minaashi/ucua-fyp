@extends('layouts.admin')

@section('content')
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Admin Panel</h1>
        </div>
    </header>
    <main class="flex-1">
        <div x-data="{ tab: 'users' }">
            <div class="mb-6">
                <nav class="flex space-x-4">
                    <button type="button" @click="tab = 'users'" :class="tab === 'users' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'" class="px-4 py-2 rounded-t-lg font-semibold shadow">User Management</button>
                    <button type="button" @click="tab = 'register'" :class="tab === 'register' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'" class="px-4 py-2 rounded-t-lg font-semibold shadow">Admin Register</button>
                    <button type="button" @click="tab = 'departments'" :class="tab === 'departments' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'" class="px-4 py-2 rounded-t-lg font-semibold shadow">Manage Departments</button>
                </nav>
            </div>
            <!-- User Management Tab -->
            <div x-show="tab === 'users'" x-cloak>
                <!-- User Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                        <p class="text-3xl font-bold text-blue-500 mt-2">{{ $totalUsers }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Admin Users</h3>
                        <p class="text-3xl font-bold text-green-500 mt-2">{{ $adminUsers }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Port Workers</h3>
                        <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $portWorkers }}</p>
                    </div>
                </div>
                <!-- Search and Filter -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <form action="{{ route('admin.users.index') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select name="role" class="border rounded px-3 py-2">
                                <option value="All Roles" {{ request('role') == 'All Roles' ? 'selected' : '' }}>All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="search" class="border rounded px-3 py-2" placeholder="Search users..." value="{{ request('search') }}">
                            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">Filter</button>
                        </div>
                    </form>
                </div>
                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Users List</h2>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 flex items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus mr-2"></i> Add New User
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->department ?? 'Not Assigned' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $user->hasRole('admin') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst(str_replace('_', ' ', $user->roles->first()->name ?? 'port_worker')) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-2">
                                                <button type="button" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Are you sure you want to delete this user?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Edit User Modal can be refactored similarly if needed -->
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-gray-500">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
            <!-- Admin Register Tab -->
            <div x-show="tab === 'register'" x-cloak>
                <div class="bg-white rounded-lg shadow-md p-8 max-w-xl mx-auto">
                    <h2 class="text-2xl font-bold mb-6 text-blue-700 flex items-center"><i class="fas fa-user-shield mr-2"></i> Register New Admin</h2>
                    <form action="{{ route('admin.register.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700">Name</label>
                            <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Email</label>
                            <input type="email" name="email" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Password</label>
                            <input type="password" name="password" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Register Admin</button>
                    </form>
                </div>
            </div>
            <!-- Manage Departments Tab -->
            <div x-show="tab === 'departments'" x-cloak>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-blue-700 flex items-center"><i class="fas fa-building mr-2"></i> Departments</h2>
                    <a href="{{ route('admin.departments.create') }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center">
                        <i class="fas fa-plus mr-2"></i> Add Department
                    </a>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Head of Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Reports</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($departments as $department)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $department->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $department->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $department->head_name }}<br>
                                            <small class="text-gray-500">{{ $department->head_email }}</small>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $department->reports_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.departments.edit', $department) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-gray-500">No departments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Add User Modal and Edit User Modal remain as before -->
@endsection 