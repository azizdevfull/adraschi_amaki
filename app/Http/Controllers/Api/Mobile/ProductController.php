<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use App\Services\GhostViewsService;
use App\Services\ProductService;

class ProductController extends Controller
{

    public function __construct(protected ProductService $productService, protected GhostViewsService $ghostViewsService)
    {
    }

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
    public function store(StoreProductRequest $request)
    {

        $product = $this->productService->add($request->category_id, $request->price, $request->discount, $request->eni, $request->gramm, $request->boyi, $request->color, $request->ishlab_chiqarish_turi_id, $request->mahsulot_tola_id, $request->brand, $request->hasFile('photos'), $request->file('photos'));

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
        $existingView = $this->ghostViewsService->getExistingView($product, $request->ip(), $request->header('User-Agent'));
        if (!$existingView) {
            $view = $this->ghostViewsService->add($request->ip(), $request->header('User-Agent'));
            $product->ghost_views()->save($view);
        }

        return response()->json([
            'status' => true,
            'data' => new ProductResource($product)
        ], 200);
    }

    public function update(UpdateProductRequest $request, string $id)
    {
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

        $this->productService->update($user, $product, $request->input('category_id', $product->category_id), $request->input('price', $product->price), $request->input('discount', $product->discount), $request->input('eni', $product->eni), $request->input('gramm', $product->gramm), $request->input('boyi', $product->boyi), $request->input('color', $product->color), $request->input('ishlab_chiqarish_turi', $product->ishlab_chiqarish_turi_id), $request->input('mahsulot_tola_id', $product->mahsulot_tola_id), $request->input('brand', $product->brand), $request->hasFile('photos'), $request->file('photos'));

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

        $this->productService->destroy($product);

        return response([
            'status' => true,
            'message' => __('product.destroy_success')
        ], 200);
    }
}
