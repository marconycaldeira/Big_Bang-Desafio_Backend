<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function getPlaylistInJSONFile(Request $request){
        return response()->json($request, 200);
    }
}
