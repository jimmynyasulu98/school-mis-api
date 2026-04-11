<?php

namespace Database\Seeders;

use App\Models\AssessmentType;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Continuous Test', 'weight' => 40],
            ['name' => 'Mid Term Exam', 'weight' => 20],
            ['name' => 'End Of Term Exam', 'weight' => 40],
        ] as $type) {
            AssessmentType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
