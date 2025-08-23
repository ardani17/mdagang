<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user - only this user can create other users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@mdagang.com',
            'password' => Hash::make('admin123'),  // Default password for admin
            'role' => 'administrator',  // lowercase to match the system
            'department' => 'Management',
            'position' => 'System Administrator',
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample regular users (optional - admin can create more)
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@mdagang.com',
                'password' => Hash::make('password123'),
                'role' => 'user',  // lowercase
                'department' => 'Production',
                'position' => 'Production Manager',
                'phone' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@mdagang.com',
                'password' => Hash::make('password123'),
                'role' => 'user',  // lowercase
                'department' => 'Sales',
                'position' => 'Sales Executive',
                'phone' => '081234567892',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@mdagang.com',
                'password' => Hash::make('password123'),
                'role' => 'user',  // lowercase
                'department' => 'Finance',
                'position' => 'Finance Officer',
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alice@mdagang.com',
                'password' => Hash::make('password123'),
                'role' => 'user',  // lowercase
                'department' => 'Inventory',
                'position' => 'Inventory Manager',
                'phone' => '081234567894',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Administrator login: admin@mdagang.com / admin123');
    }
}