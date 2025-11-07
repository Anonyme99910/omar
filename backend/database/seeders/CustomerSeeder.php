<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'أحمد محمد علي',
                'phone' => '01012345678',
                'address' => 'القاهرة، مصر الجديدة',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'فاطمة حسن',
                'phone' => '01123456789',
                'address' => 'الجيزة، المهندسين',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'محمود السيد',
                'phone' => '01234567890',
                'address' => 'الإسكندرية، سموحة',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'نور الدين أحمد',
                'phone' => '01098765432',
                'address' => 'القاهرة، مدينة نصر',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'سارة محمود',
                'phone' => '01187654321',
                'address' => 'الجيزة، الدقي',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'خالد عبد الله',
                'phone' => '01276543210',
                'address' => 'القاهرة، الزمالك',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'مريم يوسف',
                'phone' => '01065432109',
                'address' => 'الإسكندرية، ستانلي',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
            [
                'name' => 'عمر حسين',
                'phone' => '01154321098',
                'address' => 'القاهرة، التجمع الخامس',
                'total_purchases' => 0,
                'total_orders' => 0,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
