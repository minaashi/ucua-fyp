@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 text-blue-700 flex items-center">
        <i class="fas fa-building mr-2"></i> Add New Department
    </h2>
    <form action="{{ route('admin.departments.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Department Name</label>
            <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Department Email</label>
            <input type="email" name="email" class="mt-1 block w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Head of Department Name</label>
            <input type="text" name="head_name" class="mt-1 block w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Head of Department Email</label>
            <input type="email" name="head_email" class="mt-1 block w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Head of Department Phone</label>
            <input type="tel" name="head_phone" class="mt-1 block w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Active Status</label>
            <select name="is_active" class="mt-1 block w-full border rounded px-3 py-2">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary mr-2">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Create Department</button>
        </div>
    </form>
</div>
@endsection 