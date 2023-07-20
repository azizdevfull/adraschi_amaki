<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use Illuminate\Http\Request;
use App\Models\AdminUserCategory;
use App\Http\Controllers\Controller;

class AdminUserCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adminUserCategories = AdminUserCategory::all();

        return response()->json([
            'data' => $adminUserCategories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:admin_user_categories|max:255',
            'rus_name' => 'required|unique:admin_user_categories|max:255',
        ]);

        $adminUserCategory = AdminUserCategory::create($validatedData);

        return response()->json([
            'message' => __('ucategory.create_success'),
            'data' => $adminUserCategory
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $adminUserCategory = AdminUserCategory::find($id);
        return response()->json([
            'data' => $adminUserCategory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $adminUserCategory = AdminUserCategory::find($id);

        if(!$adminUserCategory){
            return response()->json([
                'message' => __('ucategory.not_found'),
            ]);
        }

        $validatedData = $request->validate([
            'name' => 'required|unique:admin_user_categories,name,' . $adminUserCategory->id . '|max:255',
            'rus_name' => 'required|unique:admin_user_categories,name,' . $adminUserCategory->id . '|max:255',
        ]);

        $adminUserCategory->update($validatedData);

        return response()->json([
            'message' => __('ucategory.update_success'),
            'data' => $adminUserCategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $adminUserCategory = AdminUserCategory::find($id);

        if(!$adminUserCategory){
            return response()->json([
                'message' => __('ucategory.not_found'),
            ]);
        }

        $adminUserCategory->delete();

        return response()->json([
            'message' => __('ucategory.destroy_success')
        ]);
    }
}
