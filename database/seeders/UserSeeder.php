<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Create staff user
        User::firstOrCreate(
            ['email' => 'chanh.staff@gmail.com'],
            [
                'name' => 'Chanh Staff',
                'password' => Hash::make('123123'),
                'role' => 'staff',
                'status' => 'active',
            ]
        );

        // Create regular test user if not exists
        User::firstOrCreate(
            ['email' => 'chanh@gmail.com'],
            [
                'name' => 'duychan',
                'password' => Hash::make('123123'),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('📧 Admin Account: admin@test.com / password');
        $this->command->info('📧 Staff Account: chanh.staff@gmail.com / 123123');
    }
}
