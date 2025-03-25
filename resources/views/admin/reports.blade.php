@extends('layouts.auth')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            <!-- Include the sidebar partial -->
            @include('admin.partials.sidebar')

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
                <div class="content-wrapper px-md-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Report Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                        
                        </div>
                    </div>

                    <!-- Report Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option>All Categories</option>
                                        <option>Unsafe Act</option>
                                        <option>Unsafe Condition</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option>All Status</option>
                                        <option>Pending</option>
                                        <option>Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Search reports...">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <!-- Your reports table content -->
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