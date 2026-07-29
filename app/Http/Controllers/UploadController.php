<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadOptionImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $dir = public_path('Images/VariationImages');
        
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $image->move($dir, $filename);

        return response()->json([
            'path' => 'Images/VariationImages/' . $filename
        ]);
    }
}