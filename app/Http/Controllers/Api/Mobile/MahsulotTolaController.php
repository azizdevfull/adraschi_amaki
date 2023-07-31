<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\MahsulotTolaRecource;
use App\Models\MahsulotTola;
use Illuminate\Http\Request;

class MahsulotTolaController extends Controller
{
    public function index(){
        $mahsulot_tolasi = MahsulotTola::all();

        return response()->json([
            'mahsulot_tolasi' => MahsulotTolaRecource::collection($mahsulot_tolasi)
        ]);
    }
}
