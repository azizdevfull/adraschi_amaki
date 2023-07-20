<?php

namespace App\Http\Resources\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // $regions = Region::all();

        // foreach ( $regions as $region ){
            // if($this->region->id == $this->region_id){
                if(App::isLocale('ru')){
                    $region_id = $this->region->rus_name;
                }else{
                    $region_id = $this->region->name;
                }
            // }else{
            //     $region_id = null;
            // }
        // }
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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'photos' => $this->photos->map(function ($photo) {
                return $photo->url;
            }),
        ];
    }
}
