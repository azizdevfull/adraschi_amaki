<?php

namespace App\Http\Controllers\Api\Mobile;
 
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LikeController extends Controller
{
    public function likePost(Request $request, $id)
    {
        // Check if the post exists
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => __('product.not_found')
            ], 404);
        }

        // Get the user's IP address and user agent
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Check if the like already exists for the same IP and user agent
        $existingLike = Like::where('product_id', $product->id)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->first();

        if ($existingLike) {
            return response()->json([
                'message' => __('like.already_liked')
            ], 400);
        }

        // Create the like for the post
        $like = Like::create([
            'product_id' => $product->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return response()->json([
            'message' => __('like.success_liked'), 
            'like' => $like
        ]);
    }

    public function unlikePost(Request $request, $id)
    {
        // Check if the post exists
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => __('product.not_found')
            ], 404);
        }

        // Get the user's IP address and user agent
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Find the like associated with the post, IP, and user agent
        $like = Like::where('product_id', $product->id)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->first();

        if (!$like) {
            return response()->json([
                'message' => __('like.not_liked')
            ], 400);
        }

        // Delete the like
        $like->delete();

        return response()->json([
            'message' => __('like.success_unlike')
        ]);
    }
}
