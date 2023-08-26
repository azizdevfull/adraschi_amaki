<?php

namespace App\Services;
use App\Models\GhostViews;

class GhostViewsService 
{
    public function getExistingView($product,$ip_address,$user_agent)
    {
        return GhostViews::where(['product_id' => $product->id,'ip' => $ip_address,'user_agent' => $user_agent,])->first();
    }

    public function add($ip_address,$user_agent)
    {
        return new GhostViews([
            'ip' => $ip_address,
            'user_agent' => $user_agent,
        ]);
    }

}

