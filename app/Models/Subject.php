<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_core'];

    protected function casts(): array
    {
        return ['is_core' => 'boolean'];
    }
}
