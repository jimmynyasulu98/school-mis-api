<?php

namespace App\Models;

use App\Models\ClassSubject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, HasUuids, Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected string $guard_name = 'api';

    protected $fillable = [
        'username',
        'password',
        'staff_id',
        'guardian_id',
        'api_token',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function isAssignedTeacherForClassSubject(ClassSubject $classSubject): bool
    {
        return $this->staff_id !== null && $classSubject->isAssignedToStaff($this->staff_id);
    }
}
