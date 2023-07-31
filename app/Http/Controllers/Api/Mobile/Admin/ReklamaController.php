<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Models\Reklama;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReklamaResource;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReklamaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reklamalar = Reklama::all();

        return response()->json([
            'status' => true,
            'data' => ReklamaResource::collection($reklamalar)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $reklama = new Reklama();
        $reklama->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $result = Cloudinary::upload(fopen($image->getRealPath(), 'r'));
                $reklama->imagesReklama()->create([
                    'url' => $result->getSecurePath()
                ]);
            }
        }
        return response([
            'status' => true,
            'message' => __('reklama.create_success'),
            'data' => new ReklamaResource($reklama)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reklama = Reklama::find($id);

        if(!$reklama){
            return response()->json([
               'status' => 'error',
               'message' => __('reklama.not_found')
            ], 404);
        }
        return response()->json([
           'status' => true,
            'data' => new ReklamaResource($reklama)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }
    
        $reklama = Reklama::find($id);
        if(!$reklama){
            return response([
               'status' => 'error',
               'message' => __('reklama.not_found')
            ], 404);
        }
      
        if ($request->hasFile('images')) {
            // delete old images if present
            $reklama->imagesReklama()->delete();
            
            // upload and save new images
            foreach ($request->file('images') as $image) {
                $result = Cloudinary::upload(fopen($image->getRealPath(), 'r'));
                $reklama->imagesReklama()->create([
                    'url' => $result->getSecurePath()
                ]);
            }
        }
        
        // update other fields if needed
        $reklama->update($request->except('images'));
    
        return response([
            'status' => true,
            'message' => __('reklama.update.success'),
            'data' => new ReklamaResource($reklama)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reklama = Reklama::find($id);
        if(!$reklama){
            return response([
              'status' => 'error',
              'message' => __('reklama.not_found')
            ], 404);
        }
        // delete the associated images from cloudinary
        // foreach ($reklama->imagesReklama as $image) {
        //     Cloudinary::destroy($image->public_id);
        // }
    
        // delete the Reklama resource
        $reklama->delete();
    
        return response([
            'status' => true,
            'message' => __('reklama.destroy_success')
        ], 200);
    }
    
}
