<?php

namespace App\Http\Controllers\Api\Mobile;

use Carbon\Carbon;
use App\Models\View;
use App\Models\Region;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 20;
        $page = intval($request->query('page')) ?? 1;
        $offset = ($page - 1) * $perPage;
    
        $products = Product::orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
    
        $total = Product::count();
    
        $lastPage = ceil($total / $perPage);
    
        $prevPageUrl = $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null;
        $nextPageUrl = $page < $lastPage ? $request->fullUrlWithQuery(['page' => $page + 1]) : null;
    
        return response()->json([
            'status' => true,
            'message' => __('product.all_success'),
            'data' => [
                'item' => ProductResource::collection($products),
                '_links' => [
                    'prevPageUrl' => $prevPageUrl,
                    'nextPageUrl' => $nextPageUrl
                ],
                '_meta' =>[
                    'total' => $total,
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'lastPage' => $lastPage,
                ]
    
            ]
        ], 200);
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
            'color' => 'nullable|string|max:255',
            'compatibility' => 'nullable|string',
            'photos' => 'array|max:4',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $product = new Product();
        $product->title = $request->title;
        $product->price = $request->price;
        $product->body = $request->body;
        $product->category_id = $request->category_id;
        $product->color = $request->color;
        $product->compatibility = $request->compatibility;
        $product->created_at = Carbon::now();
        $product->user_id = $user->id;
        $product->save();
        $product->refresh();

        $username = $user->username; // Assuming the username field exists in the User model
        $folder = 'products/' . $username;
    
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store($folder, 'public');
    
                $product->photos()->create([
                    'url' => Storage::disk('public')->url($path),
                    'public_id' => $folder, // Remove this line as it's specific to Cloudinary
                ]);
            }
        }

        return response([
            'status' => true,
            'message' => __('product.create_success'),
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response([
                'status' => 'error',
                'message' => __('product.not_found')
            ], 404);
        }
        $existingView = View::where([
            'product_id' => $product->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ])->first();
        if (!$existingView) {
            // Create a new view record
            $view = new View([
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            // Associate the view with the post
            $product->views()->save($view);
        }

        return response()->json([
            'status' => true,
            'data' => new ProductResource($product)
        ], 200);
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
        'region_id' => 'nullable|exists:regions,id',
        'color' => 'nullable|string|max:255',
        'compatibility' => 'nullable|string',
        'longitude' => 'numeric',
        'latitude' => 'numeric',
        'photos' => 'array|max:4',
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
            'message' => __('product.not_found')
        ], 404);
    }

    $user = Auth::user();

    if ($product->user_id !== $user->id) {
        return response([
            'status' => 'error',
            'message' => __('product.no_access_update')
        ], 403);
    }

    $product->title = $request->input('title', $product->title);
    $product->price = $request->input('price', $product->price);
    $product->body = $request->input('body', $product->body);
    $product->category_id = $request->input('category_id', $product->category_id);

    if($request->longitude){

        $product->longitude = $request->longitude;
    }
    if($request->latitude){
        $product->latitude = $request->latitude;
    }

    if ($request->has('region_id') && $request->input('region_id') !== null && $request->input('region_id') !== '') {
        $region = Region::find($request->input('region_id'));
        if (!$region) {
            return response([
                'status' => 'error',
                'message' => __('region.not_found')
            ], 404);
        }
        $product->region_id = $request->input('region_id');
    }
    $product->color = $request->input('color', $product->color);
    $product->compatibility = $request->input('compatibility', $product->compatibility);
    $product->updated_at = Carbon::now(); // Set the updated_at timestamp

    // $product->save();

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
    $product->refresh(); // Refresh the model to get the updated timestamps


    return response([
        'status' => true,
        'message' => __('product.update_success'),
        'data' => new ProductResource($product)
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
                'message' => __('product.not_found')
            ], 404);
        }

        $user = Auth::user();

        if ($product->user_id !== $user->id) {
            return response([
                'status' => 'error',
                'message' => __('product.no_access_delete')
            ], 403);
        }

        $product->delete();

        return response([
            'status' => true,
            'message' => __('product.destroy_success')
        ], 200);
    }
    public function toggleFavorite($id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'status' => false,
                'message' => __('product.not_found'),
            ]);
        }
        $user = Auth::user();
        if($user->favorites()->where('product_id', $product->id)->exists()){
            $user->favorites()->detach($product);
            return response()->json([
                'status' => true,
                'message' => __('product.remove_favourite')
            ]);
        } else {
            $user->favorites()->attach($product);
            return response()->json([
                'status' => true,
                'message' => __('product.add_favourite')
            ]);
        }
    }


    public function removeFavorite($id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'status' => false,
                'message' => __('product.not_found'),
            ]);
        }
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => __('auth.not_authenticated'),
            ]);
        }
        if(!$user->favorites->contains($product)){
            return response()->json([
                'status' => false,
                'message' => __('product.not_in_favorites'),
            ]);
        }
        $user->favorites()->detach($product->id);
        return response()->json([
            'status' => true,
            'message' => __('product.remove_favourite')
        ]);
    }


}
