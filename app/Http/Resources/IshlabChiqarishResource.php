<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class IshlabChiqarishResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this){
            $ishlab_chiqarish_turi_name = $this->name;
            
            if(App::isLocale('ru')){
                $ishlab_chiqarish_turi_name = $this->rus_name;
            }else if(App::isLocale('en')){
                $ishlab_chiqarish_turi_name = $this->en_name;
            }else{
                        $ishlab_chiqarish_turi_name = $this->name;
                    }
        }else{
            $ishlab_chiqarish_turi_name = null;
        }


        return [
            'id' => $this->id,
            'name' => $ishlab_chiqarish_turi_name,
        ];
    }
}
