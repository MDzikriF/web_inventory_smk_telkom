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

        User::updateOrCreate(
            ['nip' => '1234567890'],
            [
                'name' => 'Admin Inventaris',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['nip' => '0987654321'],
            [
                'name' => 'User Mahasiswa',
                'password' => bcrypt('user123'),
                'role' => 'user',
            ]
        );

        $this->call([
            CategorySeeder::class,
            UnitSeeder::class,
            ItemSeeder::class,
        ]);
    }
}
