<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = ['class_room_id', 'academic_year_id', 'term_id', 'total_amount'];

    public function classRoom() { return $this->belongsTo(ClassRoom::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term() { return $this->belongsTo(Term::class); }
    public function items() { return $this->hasMany(FeeItem::class); }
}
