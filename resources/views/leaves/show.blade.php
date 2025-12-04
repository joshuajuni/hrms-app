@extends('layouts.app')

@section('title', 'Leave Application Details')
@section('page-title', 'Leave Application Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <!-- Employee Info -->
                <div class="mb-4">
                    <h6 class="text-muted">Employee Information</h6>
                    <h5>{{ $leave->employee->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $leave->employee->employee_code }} | {{ $leave->employee->department->name ?? 'N/A' }}</p>
                </div>

                <hr>

                <!-- Leave Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Leave Type</h6>
                        <p><span class="badge bg-secondary fs-6">{{ $leave->leaveType->name }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Status</h6>
                        <p>
                            @if($leave->status === 'pending')
                                <span class="badge bg-warning fs-6">Pending</span>
                            @elseif($leave->status === 'approved')
                                <span class="badge bg-success fs-6">Approved</span>
                            @else
                                <span class="badge bg-danger fs-6">Rejected</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted">Start Date</h6>
                        <p><strong>{{ $leave->start_date->format('l, M d, Y') }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">End Date</h6>
                        <p><strong>{{ $leave->end_date->format('l, M d, Y') }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Total Days</h6>
                        <p><strong class="text-primary fs-4">{{ $leave->total_days }}</strong></p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted">Reason</h6>
                    <p class="bg-light p-3 rounded">{{ $leave->reason }}</p>
                </div>

                <!-- Approval Information -->
                @if($leave->status !== 'pending')
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Approval Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Approved/Rejected By:</strong>
                            <p>{{ $leave->approver->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Date:</strong>
                            <p>{{ $leave->approved_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($leave->approval_notes)
                    <div class="mt-2">
                        <strong>Notes:</strong>
                        <p class="bg-light p-3 rounded">{{ $leave->approval_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('leaves.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>

                    @if($leave->status === 'pending')
                        @can('update', $leave)
                        <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        @endcan

                        @can('approve', $leave)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="bi bi-check-circle me-2"></i>Approve
                        </button>
                        @endcan

                        @can('reject', $leave)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-2"></i>Reject
                        </button>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
@can('approve', $leave)
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leaves.approve', $leave) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approve Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this leave application for <strong>{{ $leave->employee->full_name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Reject Modal -->
@can('reject', $leave)
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leaves.reject', $leave) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this leave application for <strong>{{ $leave->employee->full_name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="approval_notes_reject" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="approval_notes_reject" name="approval_notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection
