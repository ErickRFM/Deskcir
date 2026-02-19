<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'mp_id',
        'brand',
        'last4'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
