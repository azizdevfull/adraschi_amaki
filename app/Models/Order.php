<?php

namespace App\Models;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'quantity', 'total','payment_type'];

    public function products()
    {
        return $this->belongsToMany(Product::class,'order_product')->withPivot('quantity');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
