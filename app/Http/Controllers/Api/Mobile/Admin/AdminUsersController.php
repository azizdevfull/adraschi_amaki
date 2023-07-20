<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Admin\UsersResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'users' => UsersResource::collection($users)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'phone' => 'required|string|unique:users|max:255',
            'tuman' => 'required|string',
            'rus_tuman' => 'required|string',
            'admin_user_category_id' =>['nullable','integer',
            Rule::exists('admin_user_categories', 'id'),
            ],
            'viloyat' => 'required|string|max:255',
            'rus_viloyat' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        $password = Hash::make($validatedData['password']);
        $validatedData['password'] = $password;

        $user = User::create($validatedData);

        return new UsersResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'User not found!'
            ]);
        }
        return response()->json([
            'status' => true,
            'user' => new UsersResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'User Not Found!',
            ]);
        }

        $validatedData = $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', Rule::unique('users')->ignore($id), 'max:255'],
            'phone' => ['sometimes', 'required', 'string', Rule::unique('users')->ignore($id), 'max:255'],
            'viloyat' => 'nullable|string|max:255',
            'rus_viloyat' => 'nullable|string|max:255',
            'product_number' => 'sometimes|required|integer',
            'phone_verified_at' => 'sometimes|date',
            'role' => 'sometimes|integer',
            'admin_user_category_id' =>['nullable','integer',Rule::exists('admin_user_categories', 'id'),],
            'avatar' => 'nullable|image|max:2048',
            'tuman'=> 'sometimes|string',
            'rus_tuman'=> 'sometimes|string',
            'views' => 'sometimes|required|integer',
            'blocked' => 'sometimes|required|integer|in:0,1',
            'password' => 'sometimes|required|string|min:6',
        ]);



        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = Str::random(20).'.'.$avatar->getClientOriginalExtension();

            // Delete old avatar if it exists
            if ($user->avatar) {
                Cloudinary::destroy($user->avatar);
                $user->avatar = null;
            }

            // Upload avatar to Cloudinary
            $uploadedFileUrl = Cloudinary::upload($avatar->getRealPath())->getSecurePath();

            $validatedData['avatar'] = $uploadedFileUrl;
        }

        if (isset($validatedData['password'])) {
            $password = Hash::make($validatedData['password']);
            $validatedData['password'] = $password;
        }

        $user->update($validatedData);

        return new UsersResource($user);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
    
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'User Not Found!',
            ]);
        }
    
        // Delete avatar if it exists
        if ($user->avatar) {
            Cloudinary::destroy($user->avatar);
        }
    
        $user->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully!',
        ]);
    }
}
