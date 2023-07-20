<?php

namespace App\Models;

use App\Models\Reklama;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImagesReklama extends Model
{
    use HasFactory;
    protected $fillable = ['filename', 'reklama_id', 'url'];

    public function reklama()
    {
        return $this->belongsTo(Reklama::class);
    }
}
