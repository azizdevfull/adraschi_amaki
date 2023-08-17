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


        if($this->category){
            $category_name = $this->category->name;
            
            if(App::isLocale('ru')){
                $category_name = $this->category->rus_name;
            }else if(App::isLocale('en')){
                $category_name = $this->category->en_name;
            }else{
                        $category_name = $this->category->name;
                    }
        }else{
            $category_name = null;
        }

        if($this->mahsulot_tola_id){
            $mahsulot_tola_name = $this->mahsulotTola->name;
            
            if(App::isLocale('ru')){
                $mahsulot_tola_name = $this->mahsulotTola->rus_name;
            }else if(App::isLocale('en')){
                $mahsulot_tola_name = $this->mahsulotTola->en_name;
            }else{
                        $mahsulot_tola_name = $this->mahsulotTola->name;
                    }
        }else{
            $mahsulot_tola_name = null;
        }

        if($this->ishlab_chiqarish_turi_id){
            $ishlab_chiqarish_turi_name = $this->ishlabChiqarishTuri->name;
            
            if(App::isLocale('ru')){
                $ishlab_chiqarish_turi_name = $this->ishlabChiqarishTuri->rus_name;
            }else if(App::isLocale('en')){
                $ishlab_chiqarish_turi_name = $this->ishlabChiqarishTuri->en_name;
            }else{
                        $ishlab_chiqarish_turi_name = $this->ishlabChiqarishTuri->name;
                    }
        }else{
            $ishlab_chiqarish_turi_name = null;
        }

        return [
            'id' => $this->id,
            'category' => $category_name,
            'price' => $this->price,
            'discount' => $this->discount,
            'sifat' => $this->sifat,
            'eni' => $this->eni,
            'boyi' => $this->boyi,
            'color' => $this->color,
            'ishlab_chiqarish_turi' => $ishlab_chiqarish_turi_name,
            'mahsulot_tola' => $mahsulot_tola_name,
            'brand' => $this->brand,
            'user' => $this->user->username,
            'views' => $this->ghost_views,
            'likes' => $this->likes()->count(),
            'views' => $this->ghost_views()->count(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'photos' => $this->photos->map(function ($photo) {
                return $photo->url;
            }),
            'owner' => new ProfileResource($this->user)
        ];
    }
}
