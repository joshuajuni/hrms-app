@extends('layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employees')

@section('page-actions')
    @can('create', App\Models\Employee::class)
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Employee
    </a>
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
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
                        <td>{{ $employee->employee_code }}</td>
                        <td>
                            <strong>{{ $employee->full_name }}</strong>
                            @if($employee->is_department_manager)
                                <span class="badge bg-info ms-1">Manager</span>
                            @endif
                        </td>
                        <td>{{ $employee->user->email }}</td>
                        <td>{{ $employee->department->name ?? '-' }}</td>
                        <td>{{ $employee->position ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst($employee->user->role->name) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('employees.show', $employee) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('update', $employee)
                                <a href="{{ route('employees.edit', $employee) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $employee)
                                <form action="{{ route('employees.destroy', $employee) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
