<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'ip_address', 'user_agent'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
