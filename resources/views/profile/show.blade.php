@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Back to Dashboard Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md">
            <!-- Header -->
            <div class="border-b px-6 py-4">
                <h1 class="text-xl font-semibold text-gray-800">Account Settings</h1>
            </div>

            <div class="p-6">
                <!-- Tabs -->
                <div class="border-b mb-4">
                    <nav class="flex space-x-8">
                        <button class="px-1 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                            General settings
                        </button>
                    </nav>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Left Column - Profile Details -->
                    <div class="md:col-span-2">
                        <h2 class="text-lg font-semibold mb-4">Profile Details</h2>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            
                            <!-- Profile Picture -->
                            <div class="mb-6">
                                <div class="flex items-center">
                                    <img src="{{ asset('images/profile.png') }}" alt="Profile Picture" 
                                         class="w-16 h-16 rounded-full">
                                    <button type="button" 
                                            class="ml-4 px-3 py-1 text-sm text-blue-600 hover:text-blue-700">
                                        Change
                                    </button>
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Name
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6">
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Column - Additional Options -->
                    <div class="md:col-span-1">
                        <!-- Change Password Section -->
                        <div class="mb-8">
                            <h2 class="text-lg font-semibold mb-4">Change password</h2>
                            <p class="text-sm text-gray-600 mb-4">
                                Update your password to maintain account security
                            </p>
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <input type="password" name="password" placeholder="New Password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <input type="password" name="password_confirmation" placeholder="Confirm Password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <button type="submit" 
                                        class="mt-4 w-full px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Change Password
                                </button>
                            </form>
                        </div>

        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">Copyright © {{ date('Y') }} Nursyahmina Mosdy, Dr Cik.Feresa Binti Mohd Foozy</p>
        </div>
    </footer>
</div>
@endsection 