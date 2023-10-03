<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'product_id', 'url', 'public_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
