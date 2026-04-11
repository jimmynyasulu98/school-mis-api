<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $table = 'student_grades';

    protected $keyType = 'string';

    protected $fillable = ['student_id', 'assessment_id', 'score', 'grade_letter', 'remarks', 'recorded_by', 'recorded_at'];

    protected function casts(): array
    {
        return ['recorded_at' => 'datetime'];
    }
}
