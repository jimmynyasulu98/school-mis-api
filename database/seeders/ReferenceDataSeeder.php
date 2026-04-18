<?php

namespace Database\Seeders;

use App\Models\AssessmentType;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Continuous Test', 'code' => 'continuous_test', 'weight' => 40, 'creation_permission' => null],
            ['name' => 'Mid Term Exam', 'code' => 'mid_term_exam', 'weight' => 20, 'creation_permission' => null],
            ['name' => 'End Of Term Exam', 'code' => 'end_of_term_exam', 'weight' => 40, 'creation_permission' => 'assessments.create.end-of-term'],
        ] as $type) {
            AssessmentType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}
