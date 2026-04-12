<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeAccount extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $table = 'student_fee_accounts';

    protected $keyType = 'string';

    protected $fillable = ['student_id', 'fee_structure_id', 'balance'];

    public function student() { return $this->belongsTo(Student::class); }
    public function feeStructure() { return $this->belongsTo(FeeStructure::class); }
    public function payments() { return $this->hasMany(Payment::class, 'student_fee_account_id'); }
}
