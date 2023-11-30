<?php

namespace App\Models\RolePermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function labelPermissions(): BelongsToMany
    {
        return $this->belongsToMany(LabelPermission::class, 'group_label_permissions')->withTimestamps();
    }

    // public function getNameAttribute($value)
    // {
    //     return ucwords(str_replace('_', ' ', $value));
    // }
}
