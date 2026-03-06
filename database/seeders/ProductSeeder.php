<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = [
            'laptops' => 'Laptops',
            'gabinetes' => 'Gabinetes',
            'refacciones' => 'Refacciones',
            'cables' => 'Cables',
            'accesorios' => 'Accesorios',
            'celulares' => 'Celulares',
        ];

        $categoryIds = [];

        foreach ($categoryMap as $slug => $name) {
            $categoryIds[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            )->id;
        }

        $products = [
            ['name' => 'Laptop HP i5', 'slug' => 'laptop-hp-i5', 'description' => 'Laptop HP Core i5 8GB RAM 512GB SSD para oficina y estudio.', 'price' => 12999, 'stock' => 10, 'category_slug' => 'laptops'],
            ['name' => 'Laptop Lenovo Ryzen 5', 'slug' => 'laptop-lenovo-ryzen-5', 'description' => 'Laptop Lenovo Ryzen 5 con SSD de 1TB para multitarea.', 'price' => 14990, 'stock' => 6, 'category_slug' => 'laptops'],
            ['name' => 'Laptop Asus Open Box', 'slug' => 'laptop-asus-open-box', 'description' => 'Laptop open box en perfecto funcionamiento con pequeno detalle estetico.', 'price' => 9990, 'stock' => 3, 'category_slug' => 'laptops'],
            ['name' => 'Gabinete Gamer RGB', 'slug' => 'gabinete-gamer-rgb', 'description' => 'Gabinete ATX con iluminacion RGB y vidrio templado.', 'price' => 1899, 'stock' => 15, 'category_slug' => 'gabinetes'],
            ['name' => 'Gabinete MicroATX Oferta', 'slug' => 'gabinete-microatx-oferta', 'description' => 'Gabinete compacto con descuento de temporada para armado economico.', 'price' => 1099, 'stock' => 12, 'category_slug' => 'gabinetes'],
            ['name' => 'Gabinete Tower Rebaja', 'slug' => 'gabinete-tower-rebaja', 'description' => 'Torre completa con rebaja especial y excelente flujo de aire.', 'price' => 1399, 'stock' => 8, 'category_slug' => 'gabinetes'],
            ['name' => 'Disco SSD 1TB', 'slug' => 'disco-ssd-1tb', 'description' => 'Unidad SSD NVMe de 1TB para alto rendimiento.', 'price' => 1499, 'stock' => 20, 'category_slug' => 'refacciones'],
            ['name' => 'Memoria RAM 16GB DDR4', 'slug' => 'memoria-ram-16gb-ddr4', 'description' => 'Modulo de memoria DDR4 de 16GB a 3200MHz.', 'price' => 899, 'stock' => 25, 'category_slug' => 'refacciones'],
            ['name' => 'Fuente 650W Certificada', 'slug' => 'fuente-650w-certificada', 'description' => 'Fuente de poder 650W con certificacion 80 Plus Bronze.', 'price' => 1290, 'stock' => 14, 'category_slug' => 'refacciones'],
            ['name' => 'Tarjeta Madre B550 Defectuosa', 'slug' => 'tarjeta-madre-b550-defectuosa', 'description' => 'Producto defectuoso para piezas o reparacion, no enciende.', 'price' => 650, 'stock' => 2, 'category_slug' => 'refacciones'],
            ['name' => 'Cable HDMI 2m', 'slug' => 'cable-hdmi-2m', 'description' => 'Cable HDMI de alta velocidad de 2 metros.', 'price' => 199, 'stock' => 50, 'category_slug' => 'cables'],
            ['name' => 'Cable USB-C 1m', 'slug' => 'cable-usbc-1m', 'description' => 'Cable USB-C reforzado para carga rapida y datos.', 'price' => 149, 'stock' => 60, 'category_slug' => 'cables'],
            ['name' => 'Cable DisplayPort 1.4', 'slug' => 'cable-displayport-1-4', 'description' => 'Cable DisplayPort para monitores de alta frecuencia.', 'price' => 249, 'stock' => 40, 'category_slug' => 'cables'],
            ['name' => 'Mouse Gamer', 'slug' => 'mouse-gamer', 'description' => 'Mouse gamer RGB de 7200 DPI con sensor de precision.', 'price' => 499, 'stock' => 30, 'category_slug' => 'accesorios'],
            ['name' => 'Teclado Mecanico', 'slug' => 'teclado-mecanico', 'description' => 'Teclado mecanico retroiluminado con switches azules.', 'price' => 899, 'stock' => 18, 'category_slug' => 'accesorios'],
            ['name' => 'Webcam 1080p', 'slug' => 'webcam-1080p', 'description' => 'Camara web Full HD con microfono integrado.', 'price' => 699, 'stock' => 0, 'category_slug' => 'accesorios'],
            ['name' => 'Audifonos Bluetooth Promo', 'slug' => 'audifonos-bluetooth-promo', 'description' => 'Audifonos inalambricos en promo con estuche de carga.', 'price' => 599, 'stock' => 22, 'category_slug' => 'accesorios'],
            ['name' => 'iPhone 13', 'slug' => 'iphone-13', 'description' => 'iPhone 13 de 128GB en excelentes condiciones.', 'price' => 15999, 'stock' => 8, 'category_slug' => 'celulares'],
            ['name' => 'Samsung Galaxy A55', 'slug' => 'samsung-galaxy-a55', 'description' => 'Smartphone Samsung con pantalla AMOLED y triple camara.', 'price' => 8999, 'stock' => 11, 'category_slug' => 'celulares'],
            ['name' => 'Xiaomi Redmi Note 13 Rebaja', 'slug' => 'xiaomi-redmi-note-13-rebaja', 'description' => 'Equipo con rebaja por lanzamiento de nueva linea.', 'price' => 5299, 'stock' => 16, 'category_slug' => 'celulares'],
        ];

        foreach ($products as $product) {
            $categorySlug = $product['category_slug'];

            Product::updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'category_id' => $categoryIds[$categorySlug],
                ]
            );
        }
    }
}