<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index()
    {
        return response()->json(Offre::all());
    }

 

 public function store(Request $request)
{
  $validated = $request->validate([
    'titre'       => 'required|string|max:255',
    'description' => 'required|string|max:65535', 
    'salaire'     => 'nullable|string|max:255',  
    'details'     => 'nullable|string|max:10000', 
]);


    $offre = Offre::create($validated);

  
    return response()->json($offre, 201);
}
    public function update(Request $request, $id)
    {
        $offre = Offre::find($id);
        if (! $offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        $validated = $request->validate([
            'titre'       => 'nullable|string|max:255',
            'description' => 'required|string|max:10000',
            'salaire'     => 'nullable|string|max:255',
            'details'     => 'nullable|string|max:255',
        ]);

       
         
  $offre->update($validated);
        return response()->json([
            'message' => 'Offre mise à jour avec succès',
            'offre'   => $offre,
        ], 200);
    }

    public function destroy($id)
    {
        $offre = Offre::find($id);
        if (! $offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        $offre->delete();
        return response()->json(['message' => 'Offre supprimée avec succès'], 200);
    }

    public function show($id)
    {
        $offre = Offre::find($id);
        if (! $offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        return response()->json($offre);
    }
      public function titles()
{
    return response()->json(
        Offre::distinct()->pluck('titre')
    );
}

    public function enregistrerOffre($offer_id)
    {
        $offre = Offre::find($offer_id);
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Vous devez être connecté pour enregistrer une offre.'], 401);
        }

        $existingRecord = Offre::where('user_id', $userId)  
                                   ->where('id', $offer_id) 
                                   ->first();

        if ($existingRecord) {
            return response()->json(['message' => 'Cette offre est déjà enregistrée !'], 400);
        }

        $offre->user_id = $userId;
        $offre->save();

        return response()->json(['message' => 'Offre enregistrée avec succès !'], 200);
    }

   public function Mesoffres()
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    $user = User::find($userId); 

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $offers = $user->offres;  
    return response()->json($offers);
}
public function deselectionnerOffre($offer_id)
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    $offre = Offre::where('id', $offer_id)
                  ->where('user_id', $userId)
                  ->first();

    if (!$offre) {
        return response()->json(['message' => "Offre non trouvée ou non enregistrée par cet utilisateur."], 404);
    }

    $offre->user_id = null;
    $offre->save();

    return response()->json(['message' => 'Offre désélectionnée avec succès !'], 200);
}

}
