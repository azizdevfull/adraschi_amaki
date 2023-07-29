<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MahsulotTola extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rus_name', 'en_name'];
    
    
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
