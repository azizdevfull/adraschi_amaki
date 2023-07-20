<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUserCategory;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        if($this->admin_user_category_id){

            $categories = AdminUserCategory::all();
            
            foreach ( $categories as $category ){
                if ($this->admin_user_category_id == $category->id){
                    $admin_user_category = $category->name;
                }
            }
            
        }else{
            $admin_user_category = null;
        }
        if($this->product_number <= 0 || $this->blocked > 0 ){
            if(App::isLocale('ru')){
                $status = 'Деактивировано';
            }else{
                $status = 'O\'chirilgan';
            }
        }else{
            if(App::isLocale('ru')){
                $status = 'Активный';
            }else{
                $status = 'Faol';
            }
        }

        if($this->blocked > 0){
            if(App::isLocale('ru')){
                $blocked = 'Заблокировано';
            }else{
                $blocked = 'Bloklangan';
            }
        }else{
            if(App::isLocale('ru')){
                $blocked = 'Разблокировано';
            }else{
                $blocked = 'Blokdan chiqarildi';
            }
        }
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'username' => $this->username,
            'phone' => $this->phone,
            'status' => $status,
            'viloyat' => $this->viloyat,
            'tuman' => $this->tuman,
            'product_number' => $this->product_number,
            'phone_verified_at' => $this->phone_verified_at,
            'role' => $this->role,
            'admin_user_category' => $admin_user_category,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'avatar' => $this->avatar,
            'views' => $this->views,
            'blocked' => $blocked,
        ];
    }
}
