<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'name' => 'Public Holiday',
                'code' => 'PUBLIC',
                'color' => '#e3342f',
                'description' => 'National public holidays',
                'is_active' => true,
            ],
            [
                'name' => 'Company Holiday',
                'code' => 'COMPANY',
                'color' => '#f6993f',
                'description' => 'Company-specific holidays',
                'is_active' => true,
            ],
            [
                'name' => 'Company Event',
                'code' => 'EVENT',
                'color' => '#3490dc',
                'description' => 'Company events and activities',
                'is_active' => true,
            ],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::create($eventType);
        }
    }
}
