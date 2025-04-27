<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        // Validation de l'image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Vérifier si un fichier est téléchargé
        if ($request->hasFile('image')) {
            // Générer un nom unique pour l'image
            $imageName = time() . '.' . $request->image->extension();

            // Sauvegarder l'image dans le dossier 'public/images'
            $request->image->move(public_path('images'), $imageName);

            // Retourner la réponse
            return response()->json([
                'message' => 'Image téléchargée avec succès',
                'image_url' => url('images/' . $imageName)
            ]);
        }

        // Si aucun fichier n'est téléchargé
        return response()->json(['error' => 'Aucune image téléchargée'], 400);
    }
}
