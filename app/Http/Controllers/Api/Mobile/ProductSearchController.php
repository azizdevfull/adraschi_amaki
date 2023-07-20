<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductSearchController extends Controller
{
    public function index(Request $request)
{
    $term = $request->input('term');
    $products = Product::search($term);
    $results = [];

    foreach ($products as $product) {
        $results[] = ['value' => new ProductResource($product)];
    }

    return response()->json($results);
}

}
