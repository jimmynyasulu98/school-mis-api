<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'description'];

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'staff_role');
    }
}
