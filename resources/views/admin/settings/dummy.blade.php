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
                        <h1 class="h2">Admin Settings</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- Settings Sections -->
                    <div class="row g-4">
                        <!-- Profile Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Profile Settings</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Admin Name</label>
                                            <input type="text" class="form-control" value="Admin User">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" value="admin@ucua.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Change Password</label>
                                            <input type="password" class="form-control" placeholder="New Password">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">System Settings</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Email Notifications</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label">Enable email notifications</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Report Auto-Archive (days)</label>
                                            <input type="number" class="form-control" value="30">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">System Theme</label>
                                            <select class="form-select">
                                                <option>Light</option>
                                                <option>Dark</option>
                                                <option>System Default</option>
                                            </select>
                                        </div>
                                    </form>
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
@endsection 