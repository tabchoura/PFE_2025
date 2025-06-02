<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('images'), $imageName);

            return response()->json([
                'message' => 'Image téléchargée avec succès',
                'image_url' => url('images/' . $imageName)
            ]);
        }

        return response()->json(['error' => 'Aucune image téléchargée'], 400);
    }
}
