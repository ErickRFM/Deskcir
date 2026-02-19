<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'file'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}