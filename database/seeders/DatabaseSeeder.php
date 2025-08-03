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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Lucas Barbosa',
            'email' => 'barbosalucaslbs96@gmail.com',
            'cpf' => '12345678909',
            'password' => bcrypt('password123'),
        ]);
    }
}
