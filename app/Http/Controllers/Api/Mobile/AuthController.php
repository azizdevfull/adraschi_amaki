<?php

namespace App\Http\Controllers\Api\Mobile;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use mrmuminov\eskizuz\Eskiz;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\ProfileResource;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $user = new User();
        $user->fullname = $request->fullname;
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->viloyat = $request->viloyat;
        $user->rus_viloyat = $request->rus_viloyat;
        $user->tuman = $request->tuman;
        $user->rus_tuman = $request->rus_tuman;
        $user->admin_user_category_id = $request->admin_user_category_id;
        $user->password = Hash::make($request->password);
        $user->created_at = Carbon::now();


        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = Str::random(20) . '.' . $avatar->getClientOriginalExtension();

            // Upload avatar to Cloudinary
            $uploadedFileUrl = Cloudinary::upload($avatar->getRealPath())->getSecurePath();

            $user->avatar = $uploadedFileUrl;
        }

        $user->save();

        // Qolgan kodlar
        $code = mt_rand(10000, 99999);

        $eskiz = new Eskiz("","");
        $result = $eskiz->requestSmsSend(
            '4546',
            'Afisha Market uchun maxsus tasdiqlovchi kodingiz: ' . $code . PHP_EOL . ' Kodni hech kimga bermang!',
            $request->phone,
            '1',
            ''
        );

        if ($result->getResponse()->isSuccess == true) {
            $key = 'phone_verification_' . $request->phone;
            Cache::put($key, $code, now()->addMinutes(5));
            return response()->json([
                'status' => $result->getResponse()->isSuccess,
                'message' => __("auth.sms_sent"),
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => __("auth.sms_failed")
            ], 500);
        }
    }


    public function login(LoginUserRequest $request)
    {
        $user = User::where(function ($query) use ($request) {
            $query->where('username', $request->login);
        })->first();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => __('auth.invalid_input')
            ], 401);
        }
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => __('auth.login_success'),
            'token' => $token,
            'user' => new ProfileResource($user)
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'string',
                Rule::exists('users', 'phone'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('auth.phone_not_found')
            ], 404);
        }

        $code = mt_rand(10000, 99999);

        $eskiz = new Eskiz("","");
        $result = $eskiz->requestSmsSend(
            '4546',
            'Afisha Market uchun maxsus tasdiqlovchi kodingiz: ' . $code . PHP_EOL . ' Kodni hech kimga bermang!',
            $request->phone,
            '1', // your-message-identity, a special identity to message
            ''
        );
        if ($result->getResponse()->isSuccess == true) {
            // Save the verification code in the cache
            $key = 'reset_password_' . $request->phone;
            Cache::put($key, $code, now()->addMinutes(5));

            // Return success response with message
            return response()->json([
                'status' => true,
                'message' => __('auth.sms_sent')
            ], 200);
        } else {
            // Return error response with message
            return response()->json([
                'status' => false,
                'message' => __('auth.sms_failed')
            ], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'string',
                Rule::exists('users', 'phone'),
            ],
            'code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = 'reset_password_' . $request->phone;
        $code = Cache::get($key);

        if (!$code || $request->code != $code) {
            return response()->json([
                'status' => false,
                'message' => __('auth.invalid_code')
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget($key);

        // Return success response with message and token
        return response()->json([
            'status' => true,
            'message' => __('auth.reset_success'),
        ], 200);
    }


    public function logoutUser(Request $request)
    {
        $accessToken = $request->bearerToken();


        $token = PersonalAccessToken::findToken($accessToken);


        $token->delete();

        return response()->json([
            'status' => true,
            'message' => __('auth.logout'),
        ], 200);
    }

    public function sendSmsDeleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('auth.user_not_found'),
            ], 404);
        }

        // Generate a new verification code
        $code = mt_rand(10000, 99999);

        $eskiz = new Eskiz("", "");
        $eskiz->requestAuthLogin();
        $result = $eskiz->requestSmsSend(
            '4546',
            'Afisha Market uchun maxsus tasdiqlovchi kodingiz: ' . $code . PHP_EOL . ' Kodni hech kimga bermang!',
            $request->phone,
            '1', // your-message-identity, a special identity to message
            ''
        );

        if ($result->getResponse()->isSuccess == true) {
            // Save the new verification code in the cache
            $key = 'phone_verification_delete_account_' . $request->phone;
            Cache::put($key, $code, now()->addMinutes(5));

            // Return success response with message
            return response()->json([
                'status' => true,
                'message' => __('auth.sms_sent')
            ], 200);
        } else {
            // Return error response with message
            return response()->json([
                'status' => false,
                'message' => __('auth.sms_failed')
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:255',
            'code' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = 'phone_verification_delete_account_' . $request->phone;
        $code = Cache::get($key);

        if (!$code || $request->code != $code) {
            return response()->json([
                'status' => false,
                'message' => __('auth.sms_invalid')
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('auth.user_not_found')
            ], 404);
        }

        // Delete the user's profile picture from Cloudinary (if any)
        if ($user->avatar) {
            Cloudinary::destroy($user->avatar);
        }

        // Revoke all personal access tokens
        $user->tokens()->delete();

        // Delete the user account
        $user->delete();

        // Return success response with message
        return response()->json([
            'status' => true,
            'message' => __('auth.delete'),
        ], 200);
    }

    // public function deleteMyAccount()
    // {
    //     $user = Auth::user();

    //     if(!$user){
    //         return response()->json([
    //             'status' => false,
    //             'message' => __('auth.user_not_found'),
    //         ]);
    //     }

    //     // Delete avatar if it exists
    //     if ($user->avatar) {
    //         Cloudinary::destroy($user->avatar);
    //     }
    //     $user->tokens()->delete();
    //     $user->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => __('auth.delete'),
    //     ]);
    // }

}
