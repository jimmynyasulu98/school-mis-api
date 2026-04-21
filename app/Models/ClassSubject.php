<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    protected $fillable = ['class_room_id', 'subject_id', 'teacher_id'];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function teacherAssignments()
    {
        return $this->hasMany(ClassSubjectTeacher::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Staff::class, 'class_subject_teachers', 'class_subject_id', 'teacher_id')
            ->withPivot('is_core', 'starts_on', 'ends_on')
            ->withTimestamps();
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function isAssignedToStaff(?string $staffId): bool
    {
        if ($staffId === null) {
            return false;
        }

        if ($this->teacher_id === $staffId) {
            return true;
        }

        if ($this->relationLoaded('teacherAssignments')) {
            return $this->teacherAssignments->contains(
                fn (ClassSubjectTeacher $assignment) => $assignment->teacher_id === $staffId && $assignment->isCurrent()
            );
        }

        return $this->teacherAssignments()->current()->where('teacher_id', $staffId)->exists();
    }
}
