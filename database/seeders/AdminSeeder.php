<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'B.A.S',
            'email' => 'BASِ@gmail.com',
            'fullname' => 'Alaa Darweesh',
            'company_name' => 'Black Analysis Solution',
            'position' => 'CEO',
            'phone_number' => '+963 999 999 999',
            'is_admin' => true,
            'password' => Hash::make('Admin12345678'),
        ]);
    }
}
