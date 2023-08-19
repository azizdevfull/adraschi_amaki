<?php

namespace App\Http\Controllers\Api\Mobile;

use Carbon\Carbon;
use App\Models\Region;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use App\Models\GhostViews;
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
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'eni' => 'required|numeric',
            'gramm' => 'required|numeric',
            'boyi' => 'required|numeric',
            'size' => 'required|numeric',
            'color' => 'required|string|max:255',
            // 'ishlab_chiqarish_turi' => 'required',
            'ishlab_chiqarish_turi' => 'required|exists:ishlab_chiqarish_turis,id',
            // 'mahsulot_turi' => 'required',
            'mahsulot_tola_id' => 'required|exists:mahsulot_tolas,id',
            'brand' => 'required',
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
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->eni = $request->eni;
        $product->gramm = $request->gramm;
        $product->boyi = $request->boyi;
        $product->size = $request->size;
        $product->color = $request->color;
        $product->ishlab_chiqarish_turi_id = $request->ishlab_chiqarish_turi;
        $product->mahsulot_tola_id = $request->mahsulot_tola_id;
        $product->brand = $request->brand;
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
        $existingView = GhostViews::where([
            'product_id' => $product->id,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ])->first();
        if (!$existingView) {
            // Create a new view record
            $view = new GhostViews([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            // Associate the view with the post
            $product->ghost_views()->save($view);
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
        // 'title' => 'string|max:255',
        // 'price' => 'numeric',
        // 'body' => 'string',
        // 'category_id' => 'exists:categories,id',
        // 'region_id' => 'nullable|exists:regions,id',
        // 'color' => 'nullable|string|max:255',
        // 'compatibility' => 'nullable|string',
        // 'longitude' => 'numeric',
        // 'latitude' => 'numeric',
        // 'photos' => 'array|max:4',
        // 'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        'category_id' => 'exists:categories,id',
        'price' => 'numeric',
        'discount' => 'nullable|numeric',
        'eni' => 'numeric',
        'gramm' => 'numeric',
        'boyi' => 'numeric',
        'size' => 'numeric',
        'color' => 'string|max:255',
        // 'ishlab_chiqarish_turi' => 'required',
        'ishlab_chiqarish_turi' => 'exists:ishlab_chiqarish_turis,id',
        // 'mahsulot_turi' => 'required',
        'mahsulot_tola_id' => 'exists:mahsulot_tolas,id',
        'brand' => 'string',
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

    $product->category_id = $request->input('category_id', $product->category_id);
    $product->price = $request->input('price', $product->price);
    $product->discount = $request->input('discount', $product->discount);
    $product->eni = $request->input('eni', $product->eni);
    $product->gramm = $request->input('gramm', $product->gramm);
    $product->boyi = $request->input('boyi', $product->boyi);
    $product->size = $request->input('size', $product->size);
    $product->color = $request->input('color', $product->color);
    $product->ishlab_chiqarish_turi_id = $request->input('ishlab_chiqarish_turi', $product->ishlab_chiqarish_turi_id);
    $product->mahsulot_tola_id = $request->input('mahsulot_tola_id', $product->mahsulot_tola_id);
    $product->brand = $request->input('brand', $product->brand);

    // $product->save();

        // $username = $user->username; // Assuming the username field exists in the User model
        // $folder = 'products/' . $username;
    
        if ($request->hasFile('photos')) {
            // Delete existing photos
            foreach ($product->photos as $photo) {
                // Extract the filename from the URL
                $filename = basename($photo->url);
                
                // Delete the photo file from storage
                Storage::disk('public')->delete('products/' . $product->user->username . '/' . $filename);
                
                // Delete the photo record from the database
                $photo->delete();
            }
            $username = $user->username; // Assuming the username field exists in the User model
            $folder = 'products/' . $username;
            // Upload and store new photos
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store($folder, 'public');
                
                $product->photos()->create([
                    'url' => Storage::disk('public')->url($path),
                    'public_id' => $folder, // Remove this line as it's specific to Cloudinary
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
        foreach ($product->photos as $photo) {
            // Extract the filename from the URL
            $filename = basename($photo->url);
            
            // Delete the photo file from storage
            Storage::disk('public')->delete('products/' . $product->user->username . '/' . $filename);
            
            // Delete the photo record from the database
            $photo->delete();
        }
        $product->delete();

        return response([
            'status' => true,
            'message' => __('product.destroy_success')
        ], 200);
    }


}
