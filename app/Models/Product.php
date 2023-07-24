<?php

namespace App\Models;

use App\Models\Like;
use App\Models\User;
use App\Models\View;
use App\Models\Photo;
use App\Models\Region;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, MediaAlly;
    protected $fillable = ['title', 'price', 'body', 'category_id', 'color', 'compatibility' ,'photos', 'views', 'user_id', 'region_id','longitude','latitude'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public static function search($term)
    {
        return self::where('title', 'LIKE', "%$term%")->get();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy(User $user)
    {
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }

    public function toggleFavorite(Product $product)
    {
        if($this->favorites()->where('product_id', $product->id)->exists()){
            $this->favorites()->attach($product);
        } else {
            $this->favorites()->attach($product);
        }
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

}
