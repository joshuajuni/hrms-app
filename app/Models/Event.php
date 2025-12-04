<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_type_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'is_recurring',
        'affects_attendance',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_recurring' => 'boolean',
            'affects_attendance' => 'boolean',
        ];
    }

    // Relationships
    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    // Scopes
    public function scopeHolidays($query)
    {
        return $query->where('affects_attendance', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q) use ($startDate, $endDate) {
                  $q->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
              });
        });
    }

    // Helper method
    public function includesDate($date)
    {
        $checkDate = Carbon::parse($date);
        return $checkDate->between($this->start_date, $this->end_date);
    }
}
