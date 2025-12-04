@extends('layouts.app')

@section('title', 'Events & Holidays')
@section('page-title', 'Events & Holidays')

@section('page-actions')
    @can('create', App\Models\Event::class)
    <a href="{{ route('events.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Event
    </a>
    @endcan
    <a href="{{ route('events.calendar') }}" class="btn btn-outline-primary">
        <i class="bi bi-calendar3 me-2"></i>Calendar View
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Recurring</th>
                        <th>Affects Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td><strong>{{ $event->title }}</strong></td>
                        <td>
                            <span class="badge" style="background-color: {{ $event->eventType->color }}">
                                {{ $event->eventType->name }}
                            </span>
                        </td>
                        <td>{{ $event->start_date->format('M d, Y') }}</td>
                        <td>{{ $event->end_date->format('M d, Y') }}</td>
                        <td>
                            @if($event->is_recurring)
                                <i class="bi bi-arrow-repeat text-primary"></i>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($event->affects_attendance)
                                <i class="bi bi-check-circle text-success"></i>
                            @else
                                <i class="bi bi-x-circle text-muted"></i>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('events.show', $event) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('update', $event)
                                <a href="{{ route('events.edit', $event) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $event)
                                <form action="{{ route('events.destroy', $event) }}" 
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
                        <td colspan="7" class="text-center text-muted py-4">No events found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection
