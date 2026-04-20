<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Amira Admin',
            'username' => 'amira',
            'email' => 'admin@example.com',
        ]);

        User::factory()->author()->create([
            'name' => 'Omar Author',
            'username' => 'omar',
            'email' => 'author@example.com',
        ]);

        User::factory()->create([
            'name' => 'Reader User',
            'username' => 'reader',
            'email' => 'reader@example.com',
            'role' => UserRole::Reader->value,
        ]);
    }
}
