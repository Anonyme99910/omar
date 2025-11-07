<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'name' => 'محمد أحمد',
                'email' => 'cashier1@perfume.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'is_active' => true,
            ],
            [
                'name' => 'فاطمة علي',
                'email' => 'cashier2@perfume.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'is_active' => true,
            ],
            [
                'name' => 'أحمد حسن',
                'email' => 'manager@perfume.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'is_active' => true,
            ],
            [
                'name' => 'سارة محمود',
                'email' => 'inventory@perfume.com',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'is_active' => true,
            ],
        ];

        foreach ($employees as $employee) {
            User::firstOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}
