<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\AdminUserCategory;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\MainCategoryResource;

class UserCategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
        $data = [];

        foreach ($categories as $category) {
            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
            ];

            if(App::isLocale('ru')) {
                $categoryData['name'] = $category->rus_name;
            }else{
                $categoryData['name'] = $category->name;
            }

            $data[] = $categoryData;
        }

        return response()->json([
            'status' => true,
            'message' => __('category.all_success'),
            'categories' => $data
        ], 200);
    }

    public function productCategories()
    {
        $categories = AdminUserCategory::all();
        // dd($categories);
        return response()->json([
            'status' => true,
            'message' => __('category.all_success'),
            'categories' => MainCategoryResource::collection($categories)
        ], 200);
    }

    public function userCategories(){
        $userCategories = AdminUserCategory::all();
        $data = [];
        foreach ($userCategories as $category) {
            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
            ];

            if(App::isLocale('ru')) {
                $categoryData['name'] = $category->rus_name;
            }else{
                $categoryData['name'] = $category->name;
            }

            $data[] = $categoryData;
        }
        return response()->json([
            'status' => true,
            'user_categories' => $data
        ]);
    }
    public function showCategory(Request $request, $id){
        $category = Category::find($id);
        // $data = [];
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => __('category.not_found'),
            ], 404);
        }
        $categoryData = [
            'id' => $category->id,
            'name' => $category->name,
        ];
       if(App::isLocale('ru')) {
            $categoryData['name'] = $category->rus_name;
        }else{
            $categoryData['name'] = $category->name;
        }

        $perPage = 20;
        $page = intval($request->query('page')) ?? 1;
        $offset = ($page - 1) * $perPage;

        $products = Product::where('category_id', $category->id)
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $total = Product::where('category_id', $category->id)->count();

        $lastPage = ceil($total / $perPage);

        $prevPageUrl = $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null;
        $nextPageUrl = $page < $lastPage ? $request->fullUrlWithQuery(['page' => $page + 1]) : null;

        return response()->json([
            'status' => true,
            'category' => $categoryData,
            'message' => "",
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

}
