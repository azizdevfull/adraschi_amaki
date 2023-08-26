<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProductService
{
    public function getTotalProduct()
    {
        return Product::count();
    }
    public function getPage($page)
    {
        return intval($page) ?? 1;
    }
    public function getOffset($page, $perPage)
    {
        return ($page - 1) * $perPage;
    }
    public function getPrevPageUrl($page, $full_url)
    {
        return $page > 1 ? $full_url : null;
    }
    public function getNextPageUrl($page, $full_url, $lastPage)
    {
        return $page < $lastPage ? $full_url : null;
    }
    public function getProducts($offset, $perPage)
    {
        return Product::orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
    }
    public function getProduct($id)
    {
        return Product::find($id);
    }

    public function add($category_id, $price, $discount, $eni, $gramm, $boyi, $color, $ishlab_chiqarish_turi_id, $mahsulot_tola_id, $brand, $has_file, $file_photos)
    {
        $user = Auth::user();
        $product = new Product();
        $product->category_id = $category_id;
        $product->price = $price;
        $product->discount = $discount;
        $product->eni = $eni;
        $product->gramm = $gramm;
        $product->boyi = $boyi;
        $product->color = $color;
        $product->ishlab_chiqarish_turi_id = $ishlab_chiqarish_turi_id;
        $product->mahsulot_tola_id = $mahsulot_tola_id;
        $product->brand = $brand;
        $product->created_at = Carbon::now();
        $product->user_id = $user->id;
        $product->save();
        $product->refresh();

        $username = $user->username;
        $folder = 'products/' . $username;

        if ($has_file) {
            foreach ($file_photos as $photo) {
                $path = $photo->store($folder, 'public');

                $product->photos()->create([
                    'url' => Storage::disk('public')->url($path),
                    'public_id' => $folder,
                ]);
            }
        }
        return $product;
    }
    public function update($user, $product, $category_id, $price, $discount, $eni, $gramm, $boyi, $color, $ishlab_chiqarish_turi_id, $mahsulot_tola_id, $brand, $has_file, $file_photos)
    {
        $product->category_id = $category_id;
        $product->price = $price;
        $product->discount = $discount;
        $product->eni = $eni;
        $product->gramm = $gramm;
        $product->boyi = $boyi;
        $product->color = $color;
        $product->ishlab_chiqarish_turi_id = $ishlab_chiqarish_turi_id;
        $product->mahsulot_tola_id = $mahsulot_tola_id;
        $product->brand = $brand;
        if ($has_file) {
            foreach ($product->photos as $photo) {
                $filename = basename($photo->url);

                Storage::disk('public')->delete('products/' . $product->user->username . '/' . $filename);

                $photo->delete();
            }
            $username = $user->username; // Assuming the username field exists in the User model
            $folder = 'products/' . $username;
            foreach ($file_photos as $photo) {
                $path = $photo->store($folder, 'public');

                $product->photos()->create([
                    'url' => Storage::disk('public')->url($path),
                    'public_id' => $folder,
                ]);
            }
        }


        $product->save();
        $product->refresh();
    }

    public function destroy($product)
    {
        foreach ($product->photos as $photo) {
            $filename = basename($photo->url);

            Storage::disk('public')->delete('products/' . $product->user->username . '/' . $filename);

            $photo->delete();
        }
        $product->delete();
    }
}
