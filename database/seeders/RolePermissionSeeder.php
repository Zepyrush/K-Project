<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolePermission\Role;
use App\Models\RolePermission\Permission;
use App\Models\RolePermission\LabelPermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin',
            'dosen',
            'mahasiswa',
        ];

        foreach ($roles as $role) {
            $role = Role::firstOrCreate(['name' => $role]);
        }

        $groupPermission = [
            'user' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'role' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'permission' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'fakultas' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'prodi' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'matakuliah' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'kelas' => [
                'create',
                'read',
                'update',
                'delete',
            ],
        ];

        foreach ($groupPermission as $group => $permissions) {
            $group = LabelPermission::firstOrCreate([
                'name' => $group,
            ]);

            foreach ($permissions as $permission) {
                $permission = Permission::firstOrCreate([
                    'name' => $group->name . '_' . $permission,
                ]);

                $permission->labelPermissions()->attach($group->id);
            }
        }
    }
}
