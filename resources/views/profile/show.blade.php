@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('page-actions')
    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>Edit Profile
    </a>
    <a href="{{ route('profile.password') }}" class="btn btn-outline-primary">
        <i class="bi bi-shield-lock me-2"></i>Change Password
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                @if($employee && $employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 150px; height: 150px; font-size: 3rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $employee->position ?? 'N/A' }}</p>
                <span class="badge bg-secondary mb-2">{{ ucfirst($user->role->name) }}</span>
                @if($employee && $employee->is_department_manager)
                    <span class="badge bg-info mb-2">Department Manager</span>
                @endif
            </div>
        </div>

        @if($employee)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Leave Balance</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Annual Leave</span>
                        <strong class="text-success">{{ $employee->annual_leave_balance }} days</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ ($employee->annual_leave_balance / 14) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Sick Leave</span>
                        <strong class="text-info">{{ $employee->sick_leave_balance }} days</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: {{ ($employee->sick_leave_balance / 14) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <!-- Account Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Role:</strong>
                        <p class="text-muted">{{ ucfirst($user->role->display_name) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Account Status:</strong>
                        <p>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email Verified:</strong>
                        <p>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Not Verified</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if($employee)
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Employee Code:</strong>
                        <p class="text-muted">{{ $employee->employee_code }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Department:</strong>
                        <p class="text-muted">{{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Phone:</strong>
                        <p class="text-muted">{{ $employee->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Date of Birth:</strong>
                        <p class="text-muted">{{ $employee->date_of_birth?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Gender:</strong>
                        <p class="text-muted">{{ ucfirst($employee->gender ?? 'N/A') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Join Date:</strong>
                        <p class="text-muted">{{ $employee->join_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <strong>Address:</strong>
                        <p class="text-muted">{{ $employee->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
