<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\IshlabChiqarishResource;
use App\Models\ishlabChiqarishTuri;
use Illuminate\Http\Request;

class IshlabChiqarishController extends Controller
{
    public function index()
    {
        $ishlab_chiqarishlar = ishlabChiqarishTuri::all();

        return response()->json([
            'ishlab_chiqarishlar' => IshlabChiqarishResource::collection($ishlab_chiqarishlar)
        ]);
    }
}
