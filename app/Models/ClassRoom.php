<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stream', 'section', 'class_teacher_id'];

    public function classTeacher() { return $this->belongsTo(Staff::class, 'class_teacher_id'); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'class_subjects'); }

    /**
     * Get the form attribute (e.g., 'Form 1' from 'Form 1A')
     */
    public function getFormAttribute(): string
    {
        // Assuming name starts with 'Form X' where X is the form number
        if (preg_match('/^(Form \d+)/', $this->name, $matches)) {
            return $matches[1];
        }
        return $this->name; // Fallback
    }
}
