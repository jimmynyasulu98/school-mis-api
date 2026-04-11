<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['student_fee_account_id', 'amount_paid', 'payment_date', 'payment_method', 'receipt_number', 'recorded_by'];

    protected function casts(): array
    {
        return ['payment_date' => 'date'];
    }
}
