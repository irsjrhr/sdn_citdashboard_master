<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityType;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $types = [
            'Meeting',
            'Field Visit',
            'Operational Support',
            'Business Trip',
            'Internal Coordination',
            'Reporting & Analysis',
            'Customer / Distributor Management',
            'Program / Project Execution',
        ];

        foreach ($types as $name) {
            ActivityType::firstOrCreate(['name' => $name]);
        }
    }
}
