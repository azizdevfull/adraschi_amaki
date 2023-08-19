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
        return [
            'id' => $this->id,
            'category' => $this->getLocalizedCategoryName(),
            'price' => $this->price,
            'discount' => $this->discount,
            'sifat' => $this->sifat,
            'eni' => $this->eni,
            'boyi' => $this->boyi,
            'size' => $this->size,
            'color' => $this->color,
            'ishlab_chiqarish_turi' => $this->getLocalizedIshlabChiqarishTuriName(),
            'mahsulot_tola' => $this->getLocalizedMahsulotTolaName(),
            'brand' => $this->brand,
            'user' => $this->user->username,
            'likes' => $this->likes()->count(),
            'views' => $this->ghost_views()->count(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'photos' => $this->photos->map(fn($photo) => $photo->url),
            'owner' => new ProfileResource($this->user),
        ];
    }
    
    protected function getLocalizedCategoryName()
    {
        return $this->getLocalizedAttribute('category', 'name');
    }
    
    protected function getLocalizedIshlabChiqarishTuriName()
    {
        return $this->getLocalizedAttribute('ishlabChiqarishTuri', 'name');
    }
    
    protected function getLocalizedMahsulotTolaName()
    {
        return $this->getLocalizedAttribute('mahsulotTola', 'name');
    }
    
    protected function getLocalizedAttribute($relation, $attribute)
    {
        $relationModel = $this->$relation;
        
        if (!$relationModel) {
            return null;
        }
        
        $localizedAttribute = $attribute;
        $locale = App::getLocale();
        
        if ($locale === 'ru' && isset($relationModel->rus_name)) {
            $localizedAttribute = 'rus_name';
        } elseif ($locale === 'en' && isset($relationModel->en_name)) {
            $localizedAttribute = 'en_name';
        }
        
        return $relationModel->$localizedAttribute;
    }
    
}
