@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Admin Settings</h1>
            <div class="flex items-center space-x-4">
                <button class="bg-white text-blue-800 px-4 py-2 rounded shadow hover:bg-gray-100 flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </div>
    </header>
    <main class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Profile Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Profile Settings</h2>
                <form>
                    <div class="mb-4">
                        <label class="block text-gray-700">Admin Name</label>
                        <input type="text" class="mt-1 block w-full border rounded px-3 py-2" value="Admin User">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" class="mt-1 block w-full border rounded px-3 py-2" value="admin@ucua.com">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Change Password</label>
                        <input type="password" class="mt-1 block w-full border rounded px-3 py-2" placeholder="New Password">
                    </div>
                </form>
            </div>
            <!-- System Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">System Settings</h2>
                <form>
                    <div class="mb-4">
                        <label class="block text-gray-700">Email Notifications</label>
                        <div class="flex items-center mt-2">
                            <input class="form-checkbox h-5 w-5 text-blue-600" type="checkbox" checked>
                            <span class="ml-2 text-gray-600">Enable email notifications</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Report Auto-Archive (days)</label>
                        <input type="number" class="mt-1 block w-full border rounded px-3 py-2" value="30">
                    </div>
                   
                </form>
            </div>
        </div>
    </main>
@endsection 