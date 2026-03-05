<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $laptops = Category::where('slug', 'laptops')->first();
        $gabinetes = Category::where('slug', 'gabinetes')->first();
        $refacciones = Category::where('slug', 'refacciones')->first();
        $cables = Category::where('slug', 'cables')->first();
        $accesorios = Category::where('slug', 'accesorios')->first();
        $celulares = Category::where('slug', 'celulares')->first();

        Product::create([
            'name' => 'Laptop HP i5',
            'slug' => 'laptop-hp-i5',
            'description' => 'Laptop HP Core i5 8GB RAM 512GB SSD',
            'price' => 12999,
            'stock' => 10,
            'category_id' => $laptops->id,
        ]);

        Product::create([
            'name' => 'Gabinete Gamer RGB',
            'slug' => 'gabinete-gamer-rgb',
            'description' => 'Gabinete ATX con iluminación RGB',
            'price' => 1899,
            'stock' => 15,
            'category_id' => $gabinetes->id,
        ]);

        Product::create([
            'name' => 'Disco SSD 1TB',
            'slug' => 'disco-ssd-1tb',
            'description' => 'Unidad SSD 1TB alta velocidad',
            'price' => 1499,
            'stock' => 20,
            'category_id' => $refacciones->id,
        ]);

        Product::create([
            'name' => 'Cable HDMI 2m',
            'slug' => 'cable-hdmi-2m',
            'description' => 'Cable HDMI alta velocidad 2 metros',
            'price' => 199,
            'stock' => 50,
            'category_id' => $cables->id,
        ]);

        Product::create([
            'name' => 'Mouse Gamer',
            'slug' => 'mouse-gamer',
            'description' => 'Mouse gamer RGB 7200 DPI',
            'price' => 499,
            'stock' => 30,
            'category_id' => $accesorios->id,
        ]);

        Product::create([
            'name' => 'iPhone 13',
            'slug' => 'iphone-13',
            'description' => 'iPhone 13 128GB',
            'price' => 15999,
            'stock' => 8,
            'category_id' => $celulares->id,
        ]);
    }
}