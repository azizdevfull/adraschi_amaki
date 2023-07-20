<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Product;
use App\Models\Category;
use App\Models\HomeView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $homeView = HomeView::firstOrCreate(['id' => 1]);
        $homeView->increment('views');
    
        $perPage = $request->get('per_page', 20);
        $products = Product::paginate($perPage);
        $categories = Category::all();
    
        $productPaginate = [
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
        ];
    
        return response()->json([
           'status' => 'success',
           'views' => $homeView->views,
           'products' => ProductResource::collection($products),
           'product_paginate' => $productPaginate,
           'categories' => $categories,
        ]);
    }
    
    
    

}