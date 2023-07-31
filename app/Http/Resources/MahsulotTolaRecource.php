<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class MahsulotTolaRecource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this){
            $mahsulot_tola_name = $this->name;
            
            if(App::isLocale('ru')){
                $mahsulot_tola_name = $this->rus_name;
            }else if(App::isLocale('en')){
                $mahsulot_tola_name = $this->en_name;
            }else{
                        $mahsulot_tola_name = $this->name;
                    }
        }else{
            $mahsulot_tola_name = null;
        }


        return [
            'id' => $this->id,
            'name' => $mahsulot_tola_name,
        ];
    }
}
