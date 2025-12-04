@extends('layouts.app')

@section('title', 'Attendance Details')
@section('page-title', 'Attendance Details')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted">Employee</h6>
                    <h5>{{ $attendance->employee->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $attendance->employee->employee_code }}</p>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Date</h6>
                        <p><strong>{{ $attendance->date->format('l, M d, Y') }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Status</h6>
                        <p>
                            @if($attendance->status === 'present')
                                <span class="badge bg-success fs-6">Present</span>
                            @elseif($attendance->status === 'late')
                                <span class="badge bg-warning fs-6">Late</span>
                            @elseif($attendance->status === 'absent')
                                <span class="badge bg-danger fs-6">Absent</span>
                            @else
                                <span class="badge bg-info fs-6">On Leave</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Check In</h6>
                        <p>
                            @if($attendance->check_in)
                                <i class="bi bi-clock-fill text-success me-2"></i>
                                <strong>{{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}</strong>
                            @else
                                <span class="text-muted">Not checked in</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Check Out</h6>
                        <p>
                            @if($attendance->check_out)
                                <i class="bi bi-clock-fill text-danger me-2"></i>
                                <strong>{{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}</strong>
                            @else
                                <span class="text-muted">Not checked out</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($attendance->notes)
                <div class="mb-3">
                    <h6 class="text-muted">Notes</h6>
                    <p>{{ $attendance->notes }}</p>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    @can('update', $attendance)
                    <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
