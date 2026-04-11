<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['first_name', 'last_name', 'phone', 'email', 'address', 'relationship'];

    public function students() { return $this->belongsToMany(Student::class)->withPivot(['is_primary', 'notes']); }
    public function user() { return $this->hasOne(User::class); }
}
