<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publicHolidayType = EventType::where('code', 'PUBLIC')->first();
        $companyHolidayType = EventType::where('code', 'COMPANY')->first();

        // Malaysia Public Holidays 2025 (Sarawak specific)
        $publicHolidays = [
            ['title' => 'New Year\'s Day', 'date' => '2025-01-01'],
            ['title' => 'Chinese New Year', 'date' => '2025-01-29'],
            ['title' => 'Chinese New Year (2nd Day)', 'date' => '2025-01-30'],
            ['title' => 'Hari Gawai Dayak', 'date' => '2025-06-01'],
            ['title' => 'Hari Gawai Dayak (2nd Day)', 'date' => '2025-06-02'],
            ['title' => 'Sarawak Independence Day', 'date' => '2025-07-22'],
            ['title' => 'Malaysia Day', 'date' => '2025-09-16'],
            ['title' => 'Christmas Day', 'date' => '2025-12-25'],
        ];

        foreach ($publicHolidays as $holiday) {
            Event::create([
                'event_type_id' => $publicHolidayType->id,
                'title' => $holiday['title'],
                'description' => 'Public Holiday',
                'start_date' => $holiday['date'],
                'end_date' => $holiday['date'],
                'is_recurring' => true,
                'affects_attendance' => true,
            ]);
        }

        // Company specific holidays
        Event::create([
            'event_type_id' => $companyHolidayType->id,
            'title' => 'Company Anniversary',
            'description' => 'Annual company celebration',
            'start_date' => '2025-03-15',
            'end_date' => '2025-03-15',
            'is_recurring' => true,
            'affects_attendance' => true,
        ]);

        Event::create([
            'event_type_id' => $companyHolidayType->id,
            'title' => 'Year End Break',
            'description' => 'Company year end closure',
            'start_date' => '2025-12-26',
            'end_date' => '2025-12-31',
            'is_recurring' => false,
            'affects_attendance' => true,
        ]);
    }
}
