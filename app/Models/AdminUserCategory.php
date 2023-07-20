<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminUserCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rus_name',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
