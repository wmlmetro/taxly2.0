<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super admin',
            'landlord',
            'tenant admin',
            'tenant user',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Define permissions
        $permissions = [
            'create invoices',
            'view invoices',
            'update invoices',
            'submit invoices',
            'delete invoices',
            'accept invoices',
            'reject invoices',
            'create webhooks',
            'view webhooks',
            'update webhooks',
            'submit webhooks',
            'delete webhooks',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign specific permissions to tenant user role
        $tenantUserPermissions = [
            'view invoices',
            'submit invoices',
            'accept invoices',
            'reject invoices',
        ];

        $tenantUser = Role::findByName('tenant user', 'web');
        $tenantUser->syncPermissions($tenantUserPermissions);

        // Assign all permissions to tenant admin role
        $tenantAdmin = Role::findByName('tenant admin', 'web');
        $tenantAdmin->syncPermissions($permissions);

        // Assign all permissions to super admin role
        $superAdmin = Role::findByName('super admin', 'web');
        $superAdmin->syncPermissions(Permission::all());
    }
}
