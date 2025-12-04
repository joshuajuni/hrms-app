@extends('layouts.app')

@section('title', 'Employee Details')
@section('page-title', 'Employee Details')

@section('page-actions')
    @can('update', $employee)
    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>Edit Employee
    </a>
    @endcan
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body text-center">
                @if($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" 
                         alt="{{ $employee->full_name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 150px; height: 150px; font-size: 3rem;">
                        {{ substr($employee->first_name, 0, 1) }}
                    </div>
                @endif
                
                <h4 class="fw-bold mb-1">{{ $employee->full_name }}</h4>
                <p class="text-muted mb-2">{{ $employee->position ?? 'N/A' }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-secondary">{{ ucfirst($employee->user->role->name) }}</span>
                    @if($employee->is_department_manager)
                        <span class="badge bg-info">Department Manager</span>
                    @endif
                </div>

                <div class="text-start mt-4">
                    <p class="mb-2">
                        <i class="bi bi-hash text-muted me-2"></i>
                        <strong>Employee Code:</strong> {{ $employee->employee_code }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-envelope text-muted me-2"></i>
                        <strong>Email:</strong> {{ $employee->user->email }}
                    </p>
                    @if($employee->phone)
                    <p class="mb-2">
                        <i class="bi bi-telephone text-muted me-2"></i>
                        <strong>Phone:</strong> {{ $employee->phone }}
                    </p>
                    @endif
                    @if($employee->department)
                    <p class="mb-2">
                        <i class="bi bi-building text-muted me-2"></i>
                        <strong>Department:</strong> {{ $employee->department->name }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Balance Card -->
        <div class="card mt-3">
            <div class="card-header bg-white">
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
    </div>

    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Date of Birth:</strong>
                        <p class="text-muted">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Gender:</strong>
                        <p class="text-muted">{{ $employee->gender ? ucfirst($employee->gender) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Join Date:</strong>
                        <p class="text-muted">{{ $employee->join_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong>
                        <p class="text-muted">
                            @if($employee->user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    @if($employee->address)
                    <div class="col-12">
                        <strong>Address:</strong>
                        <p class="text-muted">{{ $employee->address }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Leave Applications -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Leave Applications</h5>
            </div>
            <div class="card-body">
                @if($employee->leaveApplications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->leaveApplications->take(5) as $leave)
                            <tr>
                                <td>{{ $leave->leaveType->name }}</td>
                                <td>{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d') }}</td>
                                <td>{{ $leave->total_days }}</td>
                                <td>
                                    @if($leave->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($leave->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $leave->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-3">No leave applications</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
