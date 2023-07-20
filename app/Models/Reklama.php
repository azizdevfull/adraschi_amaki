<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ImagesReklama;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reklama extends Model
{
    use HasFactory;
    protected $fillable = ['images', 'category_id'];

    public function imagesReklama()
    {
        return $this->hasMany(ImagesReklama::class);
    }

}
