<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectTeacher extends Model
{
    use HasFactory;

    protected $fillable = ['class_subject_id', 'teacher_id', 'is_core'];

    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
        ];
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }
}
