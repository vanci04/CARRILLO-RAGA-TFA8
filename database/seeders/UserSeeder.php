<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['username' => 'ella.manager', 'full_name' => 'Ella Mendoza', 'email' => 'ella@example.com', 'role' => 'Manager'],
            ['username' => 'nico.barista', 'full_name' => 'Nico Ramos', 'email' => 'nico@example.com', 'role' => 'Barista'],
            ['username' => 'mia.cashier', 'full_name' => 'Mia Torres', 'email' => 'mia@example.com', 'role' => 'Cashier'],
            ['username' => 'leo.barista', 'full_name' => 'Leo Bautista', 'email' => 'leo@example.com', 'role' => 'Barista'],
            ['username' => 'ava.kitchen', 'full_name' => 'Ava Tan', 'email' => 'ava@example.com', 'role' => 'Kitchen'],
            ['username' => 'noah.cashier', 'full_name' => 'Noah Villanueva', 'email' => 'noah@example.com', 'role' => 'Cashier'],
        ];
        foreach ($records as $record) {
            User::updateOrCreate(['username' => $record['username']], $record + ['password' => Hash::make('password'), 'email_verified_at' => now()]);
        }
    }
}
