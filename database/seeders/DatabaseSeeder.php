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
        $admin = User::firstOrNew([
            'email' => 'admin@example.com',
        ]);

        $admin->forceFill([
            'name' => 'Administrador',
            'email_verified_at' => now(),
            'password' => 'password',
            'is_admin' => true,
        ])->save();
    }
}
