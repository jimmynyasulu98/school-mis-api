<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['student_id', 'class_room_id', 'term_id', 'enrollment_date', 'status'];

    protected function casts(): array
    {
        return ['enrollment_date' => 'date'];
    }
}
