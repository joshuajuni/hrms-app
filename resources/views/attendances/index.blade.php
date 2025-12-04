@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance Records')

@section('page-actions')
    @can('create', App\Models\Attendance::class)
    <a href="{{ route('attendances.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Attendance
    </a>
    @endcan
    @can('generate', App\Models\Attendance::class)
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
        <i class="bi bi-calendar-check me-2"></i>Generate Attendance
    </button>
    @endcan
    <a href="{{ route('attendances.report') }}" class="btn btn-outline-primary">
        <i class="bi bi-file-earmark-bar-graph me-2"></i>View Report
    </a>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('attendances.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" 
                       value="{{ request('date', date('Y-m-d')) }}">
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
            <div class="col-md-4">
                <label for="employee_id" class="form-label">Employee</label>
                <select name="employee_id" id="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <th>Employee</th>
                        @endif
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <td>
                            <strong>{{ $attendance->employee->full_name }}</strong>
                            <br>
                            <small class="text-muted">{{ $attendance->employee->employee_code }}</small>
                        </td>
                        @endif
                        <td>{{ $attendance->date->format('D, M d, Y') }}</td>
                        <td>
                            @if($attendance->check_in)
                                <span class="text-success">
                                    <i class="bi bi-clock-fill me-1"></i>
                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->check_out)
                                <span class="text-danger">
                                    <i class="bi bi-clock-fill me-1"></i>
                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->status === 'present')
                                <span class="badge bg-success">Present</span>
                            @elseif($attendance->status === 'late')
                                <span class="badge bg-warning">Late</span>
                            @elseif($attendance->status === 'absent')
                                <span class="badge bg-danger">Absent</span>
                            @else
                                <span class="badge bg-info">On Leave</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($attendance->notes, 30) ?? '-' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('attendances.show', $attendance) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('update', $attendance)
                                <a href="{{ route('attendances.edit', $attendance) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $attendance)
                                <form action="{{ route('attendances.destroy', $attendance) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure?');">
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
                        <td colspan="{{ auth()->user()->isAdmin() || auth()->user()->isManager() ? '7' : '6' }}" 
                            class="text-center text-muted py-4">
                            No attendance records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $attendances->links() }}
        </div>
    </div>
</div>

<!-- Generate Attendance Modal -->
@can('generate', App\Models\Attendance::class)
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('attendances.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Attendance Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="employee_id_generate" class="form-label">Employee (Optional)</label>
                        <select name="employee_id" id="employee_id_generate" class="form-select">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Leave empty to generate for all employees</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This will create attendance records with 'absent' status. Weekends and holidays will be skipped.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection
