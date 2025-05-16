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
                        <h1 class="h2">Edit Department</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Departments
                            </a>
                        </div>
                    </div>

                    <!-- Department Form -->
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Department Name*</label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $department->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Department Email*</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $department->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="head_name" class="form-label">Head of Department Name*</label>
                                        <input type="text" 
                                               class="form-control @error('head_name') is-invalid @enderror" 
                                               id="head_name" 
                                               name="head_name" 
                                               value="{{ old('head_name', $department->head_name) }}" 
                                               required>
                                        @error('head_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="head_email" class="form-label">Head of Department Email*</label>
                                        <input type="email" 
                                               class="form-control @error('head_email') is-invalid @enderror" 
                                               id="head_email" 
                                               name="head_email" 
                                               value="{{ old('head_email', $department->head_email) }}" 
                                               required>
                                        @error('head_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="head_phone" class="form-label">Head of Department Phone*</label>
                                        <input type="tel" 
                                               class="form-control @error('head_phone') is-invalid @enderror" 
                                               id="head_phone" 
                                               name="head_phone" 
                                               value="{{ old('head_phone', $department->head_phone) }}" 
                                               required>
                                        @error('head_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status">
                                            <option value="active" {{ old('status', $department->status) === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $department->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Department
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    @include('admin.partials.footer')
</div>
@endsection 