<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Ajmal',
                'name_ar' => 'أجمل',
                'description' => 'Premium Arabian perfumes',
                'is_active' => true,
            ],
            [
                'name' => 'Rasasi',
                'name_ar' => 'رصاصي',
                'description' => 'Luxury Middle Eastern fragrances',
                'is_active' => true,
            ],
            [
                'name' => 'Al Haramain',
                'name_ar' => 'الحرمين',
                'description' => 'Traditional Arabian scents',
                'is_active' => true,
            ],
            [
                'name' => 'Swiss Arabian',
                'name_ar' => 'سويس أرابيان',
                'description' => 'Blend of Eastern and Western perfumes',
                'is_active' => true,
            ],
            [
                'name' => 'Lattafa',
                'name_ar' => 'لطافة',
                'description' => 'Modern Arabian perfumes',
                'is_active' => true,
            ],
            [
                'name' => 'Nabeel',
                'name_ar' => 'نبيل',
                'description' => 'Classic Arabian fragrances',
                'is_active' => true,
            ],
            [
                'name' => 'Abdul Samad Al Qurashi',
                'name_ar' => 'عبد الصمد القرشي',
                'description' => 'Premium Oud and perfumes',
                'is_active' => true,
            ],
            [
                'name' => 'Amouage',
                'name_ar' => 'أمواج',
                'description' => 'Luxury Omani perfumes',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
