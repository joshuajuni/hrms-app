@extends('layouts.app')

@section('title', 'Leave Applications')
@section('page-title', 'Leave Applications')

@section('page-actions')
    @can('create', App\Models\LeaveApplication::class)
    <a href="{{ route('leaves.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Apply for Leave
    </a>
    @endcan
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('leaves.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Leave Applications Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <th>Employee</th>
                        @endif
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveApplications as $leave)
                    <tr>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <td>
                            <strong>{{ $leave->employee->full_name }}</strong>
                            <br>
                            <small class="text-muted">{{ $leave->employee->employee_code }}</small>
                        </td>
                        @endif
                        <td>
                            <span class="badge bg-secondary">{{ $leave->leaveType->name }}</span>
                        </td>
                        <td>{{ $leave->start_date->format('M d, Y') }}</td>
                        <td>{{ $leave->end_date->format('M d, Y') }}</td>
                        <td><strong>{{ $leave->total_days }}</strong></td>
                        <td>{{ Str::limit($leave->reason, 30) }}</td>
                        <td>
                            @if($leave->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($leave->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('view', $leave)
                                <a href="{{ route('leaves.show', $leave) }}" 
                                class="btn btn-sm btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endcan
                                @can('update', $leave)
                                <a href="{{ route('leaves.edit', $leave) }}" 
                                class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $leave)
                                <form action="{{ route('leaves.destroy', $leave) }}" 
                                    method="POST" 
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this leave application?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('cancel', $leave)
                                <form action="{{ route('leaves.cancel', $leave) }}" 
                                    method="POST" 
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to cancel this approved leave? Your leave balance will be restored.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Cancel Leave">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() || auth()->user()->isManager() ? '8' : '7' }}" 
                            class="text-center text-muted py-4">
                            No leave applications found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $leaveApplications->links() }}
        </div>
    </div>
</div>
@endsection
