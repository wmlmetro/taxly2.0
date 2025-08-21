<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles/permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // Create Super Admin user and assign role
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('!12345678'),
        ]);

        $superAdmin->assignRole('super admin');
    }
}
