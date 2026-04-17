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

    
   $categories = [
        'Eletrônicos',
        'Alimentos',
        'Bebidas',
        'Doméstico',
        'Ferramentas',
        'Automotivo', 
        'Papelaria',
        'Saúde e Beleza',
        'Outros',
    ];

    foreach ($categories as $name) {
        \App\Models\Category::create(['name' => $name]);
    }}
}
 