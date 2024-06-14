<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class ProductTransaction extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table      = 'product_transaction';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id',
        'quantity',
        'shop_id',
        'user_id',
        'payment',
        'status',
        'real_price',
        'seller_done',
        'address'
    ];
}
