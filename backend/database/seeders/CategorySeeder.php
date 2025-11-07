<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Men Perfumes',
                'name_ar' => 'عطور رجالية',
                'description' => 'Premium perfumes for men',
                'is_active' => true,
            ],
            [
                'name' => 'Women Perfumes',
                'name_ar' => 'عطور نسائية',
                'description' => 'Elegant perfumes for women',
                'is_active' => true,
            ],
            [
                'name' => 'Unisex Perfumes',
                'name_ar' => 'عطور للجنسين',
                'description' => 'Versatile perfumes for everyone',
                'is_active' => true,
            ],
            [
                'name' => 'Oud',
                'name_ar' => 'عود',
                'description' => 'Traditional Arabian Oud',
                'is_active' => true,
            ],
            [
                'name' => 'Bakhoor',
                'name_ar' => 'بخور',
                'description' => 'Incense and Bakhoor',
                'is_active' => true,
            ],
            [
                'name' => 'Oils',
                'name_ar' => 'زيوت عطرية',
                'description' => 'Concentrated perfume oils',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
