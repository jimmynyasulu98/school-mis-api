<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $table = 'staff';

    protected $keyType = 'string';

    protected $fillable = ['employee_number', 'first_name', 'last_name', 'gender', 'phone', 'email', 'job_title', 'hire_date', 'status'];

    protected function casts(): array
    {
        return ['hire_date' => 'date'];
    }

    public function user() { return $this->hasOne(User::class); }
    public function classSubjects() { return $this->belongsToMany(ClassSubject::class, 'class_subject_teachers', 'teacher_id', 'class_subject_id')->withPivot('is_core')->withTimestamps(); }
}
