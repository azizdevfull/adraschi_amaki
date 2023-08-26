<?php

namespace App\Http\Controllers\Api\Mobile;

use Carbon\Carbon;
use App\Models\Region;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use App\Models\GhostViews;
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller
{

    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 20;
        $page = $this->productService->getPage($request->query('page'));
        $offset = $this->productService->getOffset($page, $perPage);

        $products = $this->productService->getProducts($offset, $perPage);

        $total = $this->productService->getTotalProduct();

        $lastPage = ceil($total / $perPage);

        $prevPageUrl = $this->productService->getPrevPageUrl($page, $request->fullUrlWithQuery(['page' => $page - 1]));
        $nextPageUrl = $this->productService->getNextPageUrl($page, $request->fullUrlWithQuery(['page' => $page + 1]), $lastPage);

        return response()->json([
            'status' => true,
            'message' => __('product.all_success'),
            'data' => [
                'item' => ProductResource::collection($products),
                '_links' => [
                    'prevPageUrl' => $prevPageUrl,
                    'nextPageUrl' => $nextPageUrl
                ],
                '_meta' => [
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
    public function store(StoreProductRequest $request)
    {

        $product = $this->productService->add($request->category_id, $request->price, $request->discount, $request->eni, $request->gramm, $request->boyi, $request->color, $request->ishlab_chiqarish_turi_id, $request->mahsulot_tola_id, $request->brand, $request->created_at, $request->hasFile('photos'), $request->file('photos'));

        return response([
            'status' => true,
            'message' => __('product.create_success'),
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $product = $this->productService->getProduct($id);
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
            $view = new GhostViews([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            $product->ghost_views()->save($view);
        }

        return response()->json([
            'status' => true,
            'data' => new ProductResource($product)
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:categories,id',
            'price' => 'string',
            'discount' => 'nullable|string',
            'eni' => 'nullable|string',
            'gramm' => 'string',
            'boyi' => 'nullable|string',
            'color' => 'string|max:255',
            'ishlab_chiqarish_turi' => 'exists:ishlab_chiqarish_turis,id',
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

        $product = $this->productService->getProduct($id);

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
        $product->color = $request->input('color', $product->color);
        $product->ishlab_chiqarish_turi_id = $request->input('ishlab_chiqarish_turi', $product->ishlab_chiqarish_turi_id);
        $product->mahsulot_tola_id = $request->input('mahsulot_tola_id', $product->mahsulot_tola_id);
        $product->brand = $request->input('brand', $product->brand);
        if ($request->hasFile('photos')) {
            foreach ($product->photos as $photo) {
                $filename = basename($photo->url);

                Storage::disk('public')->delete('products/' . $product->user->username . '/' . $filename);

                $photo->delete();
            }
            $username = $user->username; // Assuming the username field exists in the User model
            $folder = 'products/' . $username;
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store($folder, 'public');

                $product->photos()->create([
                    'url' => Storage::disk('public')->url($path),
                    'public_id' => $folder, // Remove this line as it's specific to Cloudinary
                ]);
            }
        }


        $product->save();
        $product->refresh();

        return response([
            'status' => true,
            'message' => __('product.update_success'),
            'data' => new ProductResource($product)
        ], 200);
    }

    public function destroy(string $id)
    {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return response([
                'status' => 'error',
                'message' => __('product.not_found')
            ], 404);
        }
        foreach ($product->photos as $photo) {
            $filename = basename($photo->url);

            Storage::disk('public')->delete('products/' . $product->user->username . '/' . $filename);

            $photo->delete();
        }
        $product->delete();

        return response([
            'status' => true,
            'message' => __('product.destroy_success')
        ], 200);
    }
}
