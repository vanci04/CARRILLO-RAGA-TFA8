<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['checkout_token', 'service', 'items', 'total', 'cash'];

    protected function casts(): array
    {
        return ['items' => 'array'];
    }
}
