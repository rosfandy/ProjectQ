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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $member = Role::create(['name' => 'member', 'guard_name' => 'web']);

        Artisan::call('shield:generate', ['--all' => true]);

        $superAdmin->givePermissionTo(Permission::all());
        $member->givePermissionTo(Permission::where('name', 'like', '%view%')->get());
    }
}
