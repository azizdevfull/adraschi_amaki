<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Models\Region;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Admin\ProductsResource;

class RegionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::all();
        $data = [];

        foreach ($regions as $region) {
            $regionData = [
                'id' => $region->id,
                'name' => $region->name,
            ];

            if(App::isLocale('ru')) {
                $regionData['name'] = $region->rus_name;
            }else{
                $categoryData['name'] = $region->name;
            }

            $data[] = $regionData;
        }

        return response()->json([
            'status' =>  true,
            'message' => __('region.all_success'),
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:regions',
            'rus_name' => 'required|string|unique:regions',
        ]);

        $region = Region::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => __('region.create_success'),
            'data' => $region
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $region = Region::find($id);

        if(!$region){
            return response()->json([
                'status' => false,
                'message' => __('region.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('region.show_success'),
            'data' => $region
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $region = Region::find($id);
        if($region){
        $validatedData = $request->validate([
            'name' => 'required|string|unique:categories,name,' . $region->id,
            'rus_name' => 'required|string|unique:categories,name,' . $region->id,
        ]);

        $region->update($validatedData);

        return response()->json([
            'status' => true,
            'message' => __('region.update_success'),
            'data' => $region
        ], 200);
    }
    else{
        return response()->json([
            'status' => false,
            'message' => __('region.not_found')
            ], 404);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $region = Region::find($id);
        if($region){
            $region->delete();

            return response()->json([
                'status' => true,
                'message' => __('region.destroy_success')
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => __('region.not_found')
                ], 404);
        }
    }


    public function showRegion(Request $request, $id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json([
                'status' => false,
                'message' => __('region.not_found'),
            ], 404);
        }

        if(App::isLocale('ru')){
            $region->name = $region->rus_name;
        }else{
            $region->name = $region->name;
        }

        $perPage = 20;
        $page = intval($request->query('page')) ?? 1;
        $offset = ($page - 1) * $perPage;

        $products = Product::where('region_id', $region->id)
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $total = Product::where('region_id', $region->id)->count();

        $lastPage = ceil($total / $perPage);

        $prevPageUrl = $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null;
        $nextPageUrl = $page < $lastPage ? $request->fullUrlWithQuery(['page' => $page + 1]) : null;

        return response()->json([
            'status' => true,
            'region' => [
                'id' => $region->id,
                'name' => $region->name,
                'created_at' => $region->created_at
            ],
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
