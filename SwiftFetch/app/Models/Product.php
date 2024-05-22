<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table      = 'product';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_name',
        'price',
        'quantity',
        'shop_id'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'foreign_key', 'other_key');

    }
}

