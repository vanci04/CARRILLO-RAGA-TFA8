<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['full_name' => 'Isabel Santos', 'email' => 'isabel@example.com', 'phone' => '0917 555 0101'],
            ['full_name' => 'Miguel Reyes', 'email' => 'miguel@example.com', 'phone' => '0917 555 0102'],
            ['full_name' => 'Sofia Cruz', 'email' => 'sofia@example.com', 'phone' => '0917 555 0103'],
            ['full_name' => 'Lucas Garcia', 'email' => 'lucas@example.com', 'phone' => '0917 555 0104'],
            ['full_name' => 'Amara Flores', 'email' => 'amara@example.com', 'phone' => '0917 555 0105'],
            ['full_name' => 'Ethan Lim', 'email' => 'ethan@example.com', 'phone' => '0917 555 0106'],
        ];
        foreach ($records as $record) {
            Customer::updateOrCreate(['email' => $record['email']], $record);
        }
    }
}
