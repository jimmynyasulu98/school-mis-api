<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'enrollment_date',
        'status',
        'current_class_room_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
        ];
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'student_guardian')->withPivot(['is_primary', 'notes']);
    }
    public function currentClassRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'current_class_room_id');
    }
    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }
    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }
    public function feeAccounts()
    {
        return $this->hasMany(StudentFeeAccount::class);
    }
    public function gradeHistories()
    {
        return $this->hasMany(StudentGradeHistory::class);
    }
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class);
    }
}
