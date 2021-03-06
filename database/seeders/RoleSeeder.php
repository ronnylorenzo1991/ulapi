<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name' => 'administrador']);
        $vigilante = Role::create(['name' => 'cliente']);

        Permission::create(['name' => 'settings.turns.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.turns.edit'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.turns.show'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.turns.delete'])->syncRoles([$admin]);

        Permission::create(['name' => 'settings.users.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.users.edit'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.users.delete'])->syncRoles([$admin]);

        Permission::create(['name' => 'settings.roles.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.roles.edit'])->syncRoles([$admin]);
        Permission::create(['name' => 'settings.roles.delete'])->syncRoles([$admin]);

        Permission::create(['name' => 'dashboard.filter.date'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.filter.source'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.filter.weights'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.events.list'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.events.show'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.events.delete'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.cameras.list'])->syncRoles([$admin, $vigilante]);
        Permission::create(['name' => 'dashboard.cameras.show'])->syncRoles([$admin, $vigilante]);
    }
}
