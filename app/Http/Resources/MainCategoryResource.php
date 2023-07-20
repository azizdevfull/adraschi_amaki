<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class MainCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if(App::isLocale('ru')){
            $category_name = $this->rus_name;
        }else if(App::isLocale('en')){
            $category_name = $this->en_name;
        }else{
            $category_name = $this->name;

        }

        return [
            'id' => $this->id,
            'name' => $category_name,
            'sub_categories' => SubCategoryResource::collection($this->categories),
        ];
    }
}
