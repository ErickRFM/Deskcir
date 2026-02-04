<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cctv = Category::where('slug', 'cctv')->first();
        $alarmas = Category::where('slug', 'alarmas')->first();

        Product::create([
            'name' => 'Cámara Bullet HD',
            'slug' => 'camara-bullet-hd',
            'description' => 'Cámara de seguridad HD para exterior',
            'price' => 1199,
            'category_id' => $cctv->id,
        ]);

        Product::create([
            'name' => 'Cámara Domo',
            'slug' => 'camara-domo',
            'description' => 'Cámara domo interior 5MP',
            'price' => 899,
            'category_id' => $cctv->id,
        ]);

        Product::create([
            'name' => 'Kit Alarma',
            'slug' => 'kit-alarma',
            'description' => 'Sistema de alarma inalámbrica',
            'price' => 2499,
            'category_id' => $alarmas->id,
        ]);
    }
}
