<?php

namespace App\Models\RolePermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelPermission extends Model
{
    use HasFactory;

    protected $table = 'label_permissions';

    protected $fillable = [
        'name',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_label_permissions')->withTimestamps();
    }
}
