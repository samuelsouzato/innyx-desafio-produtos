<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@innyx.com',
            'password' => bcrypt('admin1234'), 
        ]);

    \App\Models\Category::create(['name' => 'Eletronicos']);
    \App\Models\Category::create(['name' => 'Alimentos']);
    \App\Models\Category::create(['name' => 'Limpeza']);
    \App\Models\Category::create(['name' => 'Bebidas']);
    }
}
