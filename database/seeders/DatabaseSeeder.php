<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Main Campus Admin',
            'email' => 'admin@main.com',
        ])->assignRole('main-admin');

        User::factory()->create([
            'name' => 'Morelos Campus Admin',
            'email' => 'admin@morelos.com',
        ])->assignRole('morelos-admin');

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@super.com',
        ])->assignRole('super-admin');
    }
}
