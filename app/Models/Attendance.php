<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $table = 'attendance';

    protected $keyType = 'string';

    protected $fillable = ['student_id', 'class_room_id', 'attendance_date', 'status', 'recorded_by'];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}
