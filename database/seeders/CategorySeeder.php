<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Books', 'slug' => 'books'],
            ['name' => 'School Supplies', 'slug' => 'school-supplies'],
            ['name' => 'Uniforms', 'slug' => 'uniforms'],
            ['name' => 'Urian Merchandise', 'slug' => 'urian-merchandise'],
        ]);
    }
}
