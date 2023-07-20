<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Admin\ProductsResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AdminProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => true,
            'products' => ProductsResource::collection($products)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'region_id' => 'required|exists:regions,id',
            'color' => 'nullable|string|max:255',
            'compatibility' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $product = new Product();
        $product->title = $request->title;
        $product->price = $request->price;
        $product->body = $request->body;
        $product->category_id = $request->category_id;
        $product->region_id = $request->region_id;
        $product->color = $request->color;
        $product->compatibility = $request->compatibility;
        $product->user_id = $request->user_id;
        $product->save();

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $result = Cloudinary::upload(fopen($photo->getRealPath(), 'r'));
                $product->photos()->create([
                    'url' => $result->getSecurePath()
                ]);
            }
        }
        return response([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => new ProductsResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'price' => 'numeric',
            'body' => 'string',
            'category_id' => 'exists:categories,id',
            'region_id' => 'exists:regions,id',
            'user_id' => 'required|exists:users,id',
            'color' => 'nullable|string|max:255',
            'compatibility' => 'nullable|string',
            'views' => 'sometimes|required|integer',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }
    
        $product = Product::find($id);
    
        if (!$product) {
            return response([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }
    
        $user = $request->user_id;
    

    
        $product->fill($request->only([
            'title',
            'price',
            'body',
            'category_id',
            'region_id',
            'color',
            'views',
            'user_id',
            'compatibility'
        ]));
    
        
        if ($request->hasFile('photos')) {
            // Delete existing photos for this product
            
            foreach ($product->photos as $photo) {
                Cloudinary::destroy($photo->id);
                $photo->delete();
            }
            // Add new photos for this product
            foreach ($request->file('photos') as $photo) {
                $result = Cloudinary::upload(fopen($photo->getRealPath(), 'r'));
                $product->photos()->create([
                    'url' => $result->getSecurePath()
                ]);
            }
        }

        $product->save();
        
        return response([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductsResource($product)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }
        $product->delete();
        return response([
            'status' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
