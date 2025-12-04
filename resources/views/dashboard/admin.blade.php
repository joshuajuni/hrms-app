@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Welcome back, ' . auth()->user()->name)

@section('content')
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Employees</p>
                        <h3 class="fw-bold mb-0">{{ $totalEmployees }}</h3>
                    </div>
                    <div class="fs-1 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Departments</p>
                        <h3 class="fw-bold mb-0">{{ $totalDepartments }}</h3>
                    </div>
                    <div class="fs-1 text-success">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pending Leaves</p>
                        <h3 class="fw-bold mb-0">{{ $pendingLeaves }}</h3>
                    </div>
                    <div class="fs-1 text-warning">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Today's Attendance</p>
                        <h3 class="fw-bold mb-0">{{ $todayAttendance }}</h3>
                    </div>
                    <div class="fs-1 text-info">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Leave Applications -->
    <div class="col-lg-8">
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
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLeaves as $leave)
                            <tr>
                                <td>{{ $leave->employee->full_name }}</td>
                                <td>{{ $leave->leaveType->name }}</td>
                                <td>{{ $leave->total_days }} day(s)</td>
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
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-4">
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
@endsection
