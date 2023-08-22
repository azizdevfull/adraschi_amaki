<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\GhostViews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class GhostViewsController extends Controller
{
    public function index(Request $request)
    {
        $existingViews = GhostViews::where([
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ])->get();

        $products = $existingViews->pluck('product');

        return response()->json([
            'products' => ProductResource::collection($products)
        ]);
    }
}
