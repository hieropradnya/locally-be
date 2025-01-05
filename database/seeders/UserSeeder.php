<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'riandika',
                'email' => 'user1@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '081234567891',
                'profile_picture' => null,
                'gender' => 'male',
                'role' => 'buyer',
            ],
            [
                'username' => 'lindarina',
                'email' => 'user2@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '081234567892',
                'profile_picture' => null,
                'gender' => 'female',
                'role' => 'buyer',
            ],
            [
                'username' => 'bersaku',
                'email' => 'user3@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '081234567893',
                'profile_picture' => null,
                'gender' => 'male',
                'role' => 'buyer',
            ],
            [
                'username' => 'agung12',
                'email' => 'user4@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '081234567894',
                'profile_picture' => null,
                'gender' => 'male',
                'role' => 'buyer',
            ],
            [
                'username' => 'kamala99',
                'email' => 'user5@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '081234567895',
                'profile_picture' => null,
                'gender' => 'female',
                'role' => 'buyer',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
