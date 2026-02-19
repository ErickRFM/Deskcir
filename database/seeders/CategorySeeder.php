<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $cats = [
            'Gabinetes',
            'Laptops',
            'Accesorios',
            'Refacciones'
        ];

        foreach($cats as $c){
            Category::firstOrCreate([
                'name' => $c
            ]);
        }
    }
}