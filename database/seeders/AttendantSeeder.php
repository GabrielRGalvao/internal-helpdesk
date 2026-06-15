<?php

namespace Database\Seeders;

use App\Models\Attendant;
use Illuminate\Database\Seeder;

class AttendantSeeder extends Seeder
{
    public function run(): void
    {
        $attendants = [
            [
                'name' => 'Alice Freeman',
                'email' => 'alice.f@company.com',
                'department' => 'IT Support',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.d@company.com',
                'department' => 'Infrastructure',
            ],
            [
                'name' => 'Sarah Jenkins',
                'email' => 'sarah.j@company.com',
                'department' => 'Hardware Operations',
            ],
        ];

        foreach ($attendants as $attendant) {
            Attendant::create($attendant);
        }
    }
}