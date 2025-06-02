@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Admin Settings</h1>
        </div>
    </header>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <main class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Profile Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Profile Settings</h2>
                <form action="{{ route('admin.settings.profile') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Admin Name</label>
                        <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror"
                               value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Email</label>
                        <input type="email" name="email" class="mt-1 block w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror"
                               value="{{ old('email', $admin->email) }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Current Password</label>
                        <input type="password" name="current_password" class="mt-1 block w-full border rounded px-3 py-2 @error('current_password') border-red-500 @enderror"
                               placeholder="Enter current password to change password">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Required only if changing password</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">New Password</label>
                        <input type="password" name="password" class="mt-1 block w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror"
                               placeholder="Leave blank to keep current password">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Must be 12-32 characters with mixed case, numbers, and symbols</p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2"
                               placeholder="Confirm new password">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </form>
            </div>
            <!-- System Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">System Settings</h2>
                <form action="{{ route('admin.settings.system') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium">Report Auto-Archive (days)</label>
                        <input type="number" name="auto_archive_days"
                               class="mt-1 block w-full border rounded px-3 py-2 @error('auto_archive_days') border-red-500 @enderror"
                               value="{{ old('auto_archive_days', $autoArchiveDays) }}"
                               min="1" max="365" required>
                        @error('auto_archive_days')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Number of days after which resolved reports will be automatically archived (1-365 days)</p>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Update System Settings
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection 