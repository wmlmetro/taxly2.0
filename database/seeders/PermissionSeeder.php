<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
  public function run(): void
  {
    $permissions = [
      'create invoices',
      'view invoices',
      'update invoices',
      'submit invoices',
      'delete invoices',
      'accept invoices',
      'reject invoices',
    ];

    foreach ($permissions as $perm) {
      Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
  }
}
