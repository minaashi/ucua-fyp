@extends('layouts.admin')

@section('content')
<div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl p-10 mx-auto mt-10 mb-10">
    <h2 class="text-3xl font-extrabold mb-8 text-blue-800 flex items-center gap-2">
        <i class="fas fa-building"></i> Add New Department
    </h2>
    <form action="{{ route('admin.departments.store') }}" method="POST">
        @csrf
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
        <div class="flex justify-center gap-4 mt-10">
            <a href="{{ route('admin.departments.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 font-semibold transition">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-bold shadow transition">Create Department</button>
        </div>
    </form>
</div>
@endsection 