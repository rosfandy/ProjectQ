<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $member = Role::create(['name' => 'member', 'guard_name' => 'web']);

        Artisan::call('shield:generate', ['--all' => true]);

        $allPermissions = Permission::all();

        $memberPermissions = $allPermissions->filter(function ($permission) {
            return str_contains($permission->name, 'view_') &&
                !str_contains($permission->name, 'user') &&
                !str_contains($permission->name, 'role');
        });

        $superAdmin->givePermissionTo($allPermissions);
        $member->givePermissionTo($memberPermissions);
    }
}
