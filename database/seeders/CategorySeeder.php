<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Laptops',
            'Gabinetes',
            'Refacciones',
            'Cables',
            'Accesorios',
            'Celulares',
        ];

        foreach ($categories as $categoryName) {
            $slug = Str::slug($categoryName);

            Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $categoryName]
            );
        }
    }
}