@extends('layouts.app')

@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('attendances.report') }}" class="row g-3">
            <div class="col-md-3">
                <label for="month" class="form-label">Month</label>
                <select name="month" id="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label">Year</label>
                <select name="year" id="year" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
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
                    <i class="bi bi-funnel me-2"></i>Generate
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Summary for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Total Days</th>
                        <th>Present</th>
                        <th>Late</th>
                        <th>Absent</th>
                        <th>On Leave</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report as $data)
                    <tr>
                        <td>
                            <strong>{{ $data['employee']->full_name }}</strong>
                            <br>
                            <small class="text-muted">{{ $data['employee']->employee_code }}</small>
                        </td>
                        <td><strong>{{ $data['total_days'] }}</strong></td>
                        <td><span class="badge bg-success">{{ $data['present'] }}</span></td>
                        <td><span class="badge bg-warning">{{ $data['late'] }}</span></td>
                        <td><span class="badge bg-danger">{{ $data['absent'] }}</span></td>
                        <td><span class="badge bg-info">{{ $data['on_leave'] }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No attendance records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
