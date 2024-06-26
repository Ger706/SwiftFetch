<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Product extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table      = 'product';
    protected $primaryKey = 'id';
    protected $fillable = [
        'detail',
        'quantity',
        'price',
        'shop_id',
        'origin',
        'product_name',
        'image',
        'remaining_stock'
    ];

    public function getProduct($productId){
        $product = self::where('id','=',$productId)->whereNull('deleted_at');
        return $product ? $product : null;
    }
}
