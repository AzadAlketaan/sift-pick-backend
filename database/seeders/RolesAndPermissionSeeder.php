<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = $this->getRoles();

        foreach ($roles as $role) {
            $isExists = Role::whereName($role['name'])->exists();
            if (!$isExists) DB::table('roles')->insert($roles);
        }

        $permissions = $this->getGeneralPermissions();

        foreach ($permissions as $permission) {
            $isExists = Permission::whereName($permission['name'])->exists();
            if (!$isExists) DB::table('permissions')->insert($permission);
        }

        $adminRole = Role::findByName('supar_admin', 'dashboard');
        foreach ($permissions as $permission) {
            $adminRole->givePermissionTo($permission['name']);
        }
    }

    private function getRoles(): array
    {
        return array(
            array('name' => 'supar_admin', 'guard_name' => 'dashboard'),
            array('name' => 'admin', 'guard_name' => 'dashboard'),
            array('name' => 'saller', 'guard_name' => 'dashboard'),
            array('name' => 'buyer', 'guard_name' => 'dashboard'),
            array('name' => 'user', 'guard_name' => 'app')
        );
    }

    private function getGeneralPermissions(): array
    {
        return array(
            array('name' => 'access_dashboard', 'display_name' => 'Access Dashboard', 'group_name' => 'General', 'guard_name' => 'dashboard'),
            array('name' => 'view_all_products', 'display_name' => 'View All Products', 'group_name' => 'Product Management', 'guard_name' => 'dashboard'),
            array('name' => 'add_product', 'display_name' => 'Add Product', 'group_name' => 'Product Management', 'guard_name' => 'dashboard'),
            array('name' => 'edit_product', 'display_name' => 'Edit Product', 'group_name' => 'Product Management', 'guard_name' => 'dashboard'),
            array('name' => 'delete_product', 'display_name' => 'Delete Product', 'group_name' => 'Product Management', 'guard_name' => 'dashboard'),
            array('name' => 'show_product', 'display_name' => 'Show Product', 'group_name' => 'Product Management', 'guard_name' => 'dashboard')
        );
    }
}
