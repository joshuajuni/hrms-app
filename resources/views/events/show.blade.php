@extends('layouts.app')

@section('title', 'Event Details')
@section('page-title', 'Event Details')

@section('page-actions')
    @can('update', $event)
    <a href="{{ route('events.edit', $event) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>Edit Event
    </a>
    @endcan
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="mb-4 text-center">
                    <span class="badge fs-5" style="background-color: {{ $event->eventType->color }}">
                        {{ $event->eventType->name }}
                    </span>
                </div>

                <h3 class="text-center mb-4">{{ $event->title }}</h3>

                @if($event->description)
                <div class="mb-4">
                    <h6 class="text-muted">Description</h6>
                    <p>{{ $event->description }}</p>
                </div>
                @endif

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Start Date</h6>
                        <p><strong>{{ $event->start_date->format('l, M d, Y') }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">End Date</h6>
                        <p><strong>{{ $event->end_date->format('l, M d, Y') }}</strong></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Recurring</h6>
                        <p>
                            @if($event->is_recurring)
                                <i class="bi bi-arrow-repeat text-primary me-2"></i>
                                <span class="text-success">Yes, Annual</span>
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Affects Attendance</h6>
                        <p>
                            @if($event->affects_attendance)
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <span class="text-success">Yes (Holiday)</span>
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('events.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
