<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProfileResource;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    public function Profile()
    {
        $user = Auth::user();
        $products = $user->products;
        // $products = Product::all();

        return response()->json([
            'status' => true,
            'user' => new ProfileResource($user),
            'products' => ProductResource::collection($products)
        ], 200);
    }

    public function ProfileUpdate(UpdateUserRequest $request)
    {
        $user = Auth::user();

        if($request->input('admin_user_category_id')){
            $user->admin_user_category_id = $request->input('admin_user_category_id');
        }
        $user->fullname = $request->input('fullname');
        // $user->viloyat = $request->input('viloyat');
        // $user->rus_viloyat = $request->input('rus_viloyat');
        // $user->tuman = $request->input('tuman');
        // $user->rus_tuman = $request->input('rus_tuman');

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = Str::random(20).'.'.$avatar->getClientOriginalExtension();

            // Delete old avatar if it exists
            if ($user->avatar) {
                Cloudinary::destroy($user->avatar);
            }

            // Upload avatar to Cloudinary
            $uploadedFileUrl = Cloudinary::upload($avatar->getRealPath())->getSecurePath();

            $user->avatar = $uploadedFileUrl;
        }

        $user->save();

        return response()->json([
            'message' => __('profile.update_success'),
            'user' => new ProfileResource($user)
        ], 200);
    }

    public function show($id){
        $user = User::find($id);

        if (!$user) {
            return response([
                'status' => false,
                'message' => __('auth.user_not_found')
            ], 404);
        }
        $user->increment('views');
        $products = $user->products;

        return response()->json([
            'status' => true,
            'user' => new ProfileResource($user),
            'products' => ProductResource::collection($products)
        ]);

    }

    public function favourites(){
        $favoriteProducts = Auth::user()->favoriteProducts();

        return response()->json([
            'favorite_products' => ProductResource::collection($favoriteProducts)
        ]);
    }


}
