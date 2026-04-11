<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['user_id', 'action', 'table_name', 'record_id', 'ip_address', 'context'];

    protected function casts(): array
    {
        return ['context' => 'array'];
    }
}
