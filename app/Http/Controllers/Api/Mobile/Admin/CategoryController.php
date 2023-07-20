<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $data = [];

        foreach ($categories as $category) {
            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
            ];


            if (App::isLocale('ru')) {
                $categoryData['name'] = $category->rus_name;
            }

            $data[] = $categoryData;
        }

        return response()->json([
            'status' => true,
            'message' => __('category.all_success'),
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:categories',
            'admin_user_category_id' => 'required|exists:admin_user_categories,id',
            'rus_name' => 'required|string|unique:categories',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->admin_user_category_id = $request->admin_user_category_id;
        $category->rus_name = $request->rus_name;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => __('category.create_success'),
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => __('category.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if ($category) {
            $validatedData = $request->validate([
                'name' => 'required|string|unique:categories,name,' . $category->id,
                'admin_user_category_id' => 'required|exists:admin_user_categories,id',
                'rus_name' => 'required|string|unique:categories,name,' . $category->id,
            ]);

            $category->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => __('category.update_success'),
                'data' => $category
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('category.not_found')
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();

            return response()->json([
                'status' => true,
                'message' => __('category.destroy_success')
            ], 204);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('category.not_found')
            ], 404);
        }
    }
}
