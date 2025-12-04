<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::with('eventType')
            ->latest('start_date')
            ->paginate(15);

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Event::class);

        $eventTypes = EventType::where('is_active', true)->get();

        return view('events.create', compact('eventTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
            'affects_attendance' => 'boolean',
        ]);

        $event = Event::create($validated);

        ActivityLogger::log('created', "Created event: {$event->title}", $event);

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->load('eventType');

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $eventTypes = EventType::where('is_active', true)->get();

        return view('events.edit', compact('event', 'eventTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
            'affects_attendance' => 'boolean',
        ]);

        $event->update($validated);

        ActivityLogger::log('updated', "Updated event: {$event->title}", $event);

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $title = $event->title;
        $event->delete();

        ActivityLogger::log('deleted', "Deleted event: {$title}");

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    public function calendar()
    {
        $events = Event::with('eventType')->get();

        // Format for FullCalendar
        $calendarEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->format('Y-m-d'),
                'end' => $event->end_date->addDay()->format('Y-m-d'),
                'color' => $event->eventType->color,
                'description' => $event->description,
            ];
        });

        return view('events.calendar', compact('calendarEvents'));
    }
}
