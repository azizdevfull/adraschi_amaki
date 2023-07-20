<?php

namespace App\Http\Resources;

use App\Models\Region;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if(App::isLocale('ru')){
            $region_id = $this->region->rus_name;
        }else{
            $region_id = $this->region->name;
        }
        if($this->category){
            $category_name = $this->category->name;
            
                    if(App::isLocale('ru')){
                        $category_name = $this->category->rus_name;
                    }else{
                        $category_name = $this->category->name;
                    }
        }else{
            $category_name = null;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'body' => $this->body,
            'category' => $category_name,
            'region' => $region_id,
            'color' => $this->color,
            'compatibility' => $this->compatibility,
            'user' => $this->user->username,
            'views' => $this->views,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'photos' => $this->photos->map(function ($photo) {
                return $photo->url;
            }),
            'owner' => new ProfileResource($this->user)
        ];
    }
}
