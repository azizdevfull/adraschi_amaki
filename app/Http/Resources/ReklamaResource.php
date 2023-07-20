<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ReklamaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->category_id){
            $categories = Category::all();
            foreach ($categories as $category) {
                if ($this->category_id == $category->id) {
                    $category_id = $category->name;
                }
            }
        } else {
            $category_id = null;
        }
        return [
            'id' => $this->id,
            'category' => $category_id,
            'images' => $this->imagesReklama->map(function ($image) {
                return $image->url;
            }),
        ];
    }
}
