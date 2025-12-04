@extends('layouts.app')

@section('title', 'Employee Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Welcome back, ' . $employee->first_name)

@section('content')
<!-- Leave Balance Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card stats-card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Annual Leave Balance</h6>
                <h2 class="fw-bold mb-2">{{ $annualLeaveBalance }} <small class="fs-6">days</small></h2>
                <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar bg-white" role="progressbar" 
                         style="width: {{ ($annualLeaveBalance / 14) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card stats-card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Sick Leave Balance</h6>
                <h2 class="fw-bold mb-2">{{ $sickLeaveBalance }} <small class="fs-6">days</small></h2>
                <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar bg-white" role="progressbar" 
                         style="width: {{ ($sickLeaveBalance / 14) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Attendance -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Today's Attendance</h5>
            </div>
            <div class="card-body">
                @if($todayAttendance)
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="fs-1 text-success me-3">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted">Check In</small>
                                <h4 class="mb-0">
                                    {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : 'Not checked in' }}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="fs-1 text-danger me-3">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted">Check Out</small>
                                <h4 class="mb-0">
                                    {{ $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') : 'Not checked out' }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge 
                        @if($todayAttendance->status === 'present') bg-success
                        @elseif($todayAttendance->status === 'late') bg-warning
                        @elseif($todayAttendance->status === 'on_leave') bg-info
                        @else bg-danger
                        @endif
                    ">
                        {{ ucfirst($todayAttendance->status) }}
                    </span>
                </div>
                @else
                <p class="text-muted text-center py-4">No attendance record for today</p>
                @endif
            </div>
        </div>

        <!-- Monthly Attendance Summary -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">This Month's Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-3">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <h4 class="text-success mb-1">{{ $attendanceSummary['present'] }}</h4>
                            <small class="text-muted">Present</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 bg-warning bg-opacity-10 rounded">
                            <h4 class="text-warning mb-1">{{ $attendanceSummary['late'] }}</h4>
                            <small class="text-muted">Late</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 bg-danger bg-opacity-10 rounded">
                            <h4 class="text-danger mb-1">{{ $attendanceSummary['absent'] }}</h4>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                            <h4 class="text-info mb-1">{{ $attendanceSummary['on_leave'] }}</h4>
                            <small class="text-muted">On Leave</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Events -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Apply for Leave
                    </a>
                    <a href="{{ route('attendances.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-clock-history me-2"></i>View Attendance
                    </a>
                    <a href="{{ route('events.calendar') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar3 me-2"></i>View Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Upcoming Events</h5>
            </div>
            <div class="card-body">
                @if($upcomingEvents->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($upcomingEvents as $event)
                    <div class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="badge" style="background-color: {{ $event->eventType->color }}">
                                    {{ $event->start_date->format('M d') }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $event->title }}</h6>
                                <small class="text-muted">{{ $event->eventType->name }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center py-4">No upcoming events</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Leave Applications -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Leave Applications</h5>
    </div>
    <div class="card-body">
        @if($recentLeaves->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
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
                    @foreach($recentLeaves as $leave)
                    <tr>
                        <td>{{ $leave->leaveType->name }}</td>
                        <td>{{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}</td>
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
        <p class="text-muted text-center py-4">No recent leave applications</p>
        @endif
    </div>
</div>
@endsection
