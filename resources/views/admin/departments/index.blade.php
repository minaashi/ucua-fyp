@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center w-full">
    <h1 class="text-2xl font-bold">Department Management</h1>
    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 flex items-center" data-toggle="modal" data-target="#addDepartmentModal">
        <i class="fas fa-plus mr-2"></i> Add New Department
    </button>
</div>
@if(session('success'))
    <div class="alert alert-success w-full">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger w-full">
        {{ session('error') }}
    </div>
@endif
<div class="bg-white rounded-lg shadow-md p-6 w-full">
    <div class="overflow-x-auto w-full">
        <table class="min-w-full w-full divide-y divide-gray-200">
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
                            <button type="button" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2" data-toggle="modal" data-target="#editDepartmentModal{{ $department->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
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

@foreach($departments as $department)
<div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" role="dialog" aria-labelledby="editDepartmentModalLabel{{ $department->id }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('admin.departments.update', $department) }}" method="POST" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="editDepartmentModalLabel{{ $department->id }}">Edit Department</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Department Name</label>
          <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Department Email</label>
          <input type="email" name="email" class="form-control" value="{{ $department->email }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Head of Department Name</label>
          <input type="text" name="head_name" class="form-control" value="{{ $department->head_name }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Head of Department Email</label>
          <input type="email" name="head_email" class="form-control" value="{{ $department->head_email }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Head of Department Phone</label>
          <input type="tel" name="head_phone" class="form-control" value="{{ $department->head_phone }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Active Status</label>
          <select name="is_active" class="form-control">
            <option value="1" {{ $department->is_active ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !$department->is_active ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Department</button>
      </div>
    </form>
  </div>
</div>
@endforeach

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ route('admin.departments.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title text-blue-800 font-bold" id="addDepartmentModalLabel">
            <i class="fas fa-building mr-2"></i> Add New Department
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Department Name</label>
                <input type="text" name="name" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Department Email</label>
                <input type="email" name="email" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Head of Department Name</label>
                <input type="text" name="head_name" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Head of Department Email</label>
                <input type="email" name="head_email" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Head of Department Phone</label>
                <input type="tel" name="head_phone" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Active Status</label>
                <select name="is_active" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-center gap-4">
        <button type="button" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 font-semibold transition" data-dismiss="modal">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-bold shadow transition">Create Department</button>
      </div>
    </form>
  </div>
</div>
@endsection 