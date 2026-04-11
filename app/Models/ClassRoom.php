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
}
