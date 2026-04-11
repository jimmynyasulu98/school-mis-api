<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['class_subject_id', 'term_id', 'assessment_type_id', 'title', 'max_score', 'assessment_date'];

    protected function casts(): array
    {
        return ['assessment_date' => 'date'];
    }

    public function grades() { return $this->hasMany(StudentGrade::class); }
}
