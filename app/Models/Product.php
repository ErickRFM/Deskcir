<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'category_id'
    ];

    // 👉 Relación con imágenes múltiples
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // 👉 Relación con categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}