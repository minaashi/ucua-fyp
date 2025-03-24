@extends('layouts.auth')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            @include('admin.partials.sidebar')

            <main class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
                <div class="content-wrapper px-md-4">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">User Management</h1>
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Add New User
                        </button>
                    </div>

                    <!-- User Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Users</h5>
                                    <h2>150</h2>
                                </div>
                            </div>
                        </div>
                        <!-- Add more stat cards -->
                    </div>

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <!-- Your users table content -->
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