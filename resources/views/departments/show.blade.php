@extends('layouts.app')

@section('title', 'Department Details')
@section('page-title', $department->name)

@section('page-actions')
    @can('update', $department)
    <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>Edit Department
    </a>
    @endcan
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Department Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Code:</strong>
                    <p class="text-muted">{{ $department->code }}</p>
                </div>
                <div class="mb-3">
                    <strong>Name:</strong>
                    <p class="text-muted">{{ $department->name }}</p>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <p>
                        @if($department->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                </div>
                @if($department->description)
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p class="text-muted">{{ $department->description }}</p>
                </div>
                @endif
                <div class="mb-3">
                    <strong>Total Employees:</strong>
                    <p class="text-muted">{{ $department->employees->count() }}</p>
                </div>
                <div>
                    <strong>Created:</strong>
                    <p class="text-muted">{{ $department->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Department Employees</h5>
            </div>
            <div class="card-body">
                @if($department->employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->employees as $employee)
                            <tr>
                                <td>
                                    @if($employee->photo)
                                        <img src="{{ asset('storage/' . $employee->photo) }}" 
                                             alt="{{ $employee->full_name }}" 
                                             class="profile-img">
                                    @else
                                        <div class="profile-img bg-secondary text-white d-flex align-items-center justify-content-center">
                                            {{ substr($employee->first_name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('employees.show', $employee) }}">
                                        {{ $employee->full_name }}
                                    </a>
                                    @if($employee->is_department_manager)
                                        <span class="badge bg-info ms-1">Manager</span>
                                    @endif
                                </td>
                                <td>{{ $employee->position ?? '-' }}</td>
                                <td>{{ $employee->user->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($employee->user->role->name) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4">No employees in this department</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
