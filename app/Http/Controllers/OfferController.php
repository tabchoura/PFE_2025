<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\User;

use Illuminate\Http\Request;
use App\Jobs\EvaluateOfferEmbedding;
use App\Services\LocalEmbeddingService;
use App\Jobs\EvaluateCvEmbedding;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    // 1. Récupère toutes les offres
    public function index()
    {
        return response()->json(Offre::all());
    }

   public function titles()
{
    // Eloquent : on applique directement distinct() puis pluck()
    return response()->json(
        Offre::distinct()->pluck('titre')
    );
}

    // 2. Crée une nouvelle offre
 public function store(Request $request)
{
    // Validation
  $validated = $request->validate([
    'titre'       => 'required|string|max:255',
    'description' => 'required|string|max:65535', // Valeur réaliste pour un champ TEXT (65kB max environ)
    'salaire'     => 'nullable|string|max:255',   // Facultatif, mais éviter les champs illimités par défaut
    'details'     => 'nullable|string|max:10000', // 10005 est un peu arbitraire, mieux vaut arrondir
]);


    $offre = Offre::create($validated);

    // Génération de l'embedding pour l'offre
    $vector = app(LocalEmbeddingService::class)
                 ->embedText($offre->description);

    // Mise à jour de l'offre avec l'embedding
    $offre->update(['embedding' => json_encode($vector)]); // Stockage sous forme JSON

    // Retourne l'offre complète (avec embedding)
    return response()->json($offre, 201);
}
    public function update(Request $request, $id)
    {
        $offre = Offre::find($id);
        if (! $offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        // Validation
        $validated = $request->validate([
            'titre'       => 'nullable|string|max:255',
            'description' => 'required|string|max:10000',
            'salaire'     => 'nullable|string|max:255',
            'details'     => 'nullable|string|max:255',
        ]);

        // Mise à jour des champs classiques
        $offre->update($validated);

        // Regénération de l'embedding si la description est fournie
        if (isset($validated['description'])) {
            $vector = app(LocalEmbeddingService::class)
                         ->embedText($offre->description);
            $offre->update(['embedding' => $vector]);
        }

        return response()->json([
            'message' => 'Offre mise à jour avec succès',
            'offre'   => $offre,
        ], 200);
    }

    // 4. Supprime une offre
    public function destroy($id)
    {
        $offre = Offre::find($id);
        if (! $offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        $offre->delete();
        return response()->json(['message' => 'Offre supprimée avec succès'], 200);
    }

    // 5. Affiche une offre
    public function show($id)
    {
        $offre = Offre::find($id);
        if (! $offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        return response()->json($offre);
    }

    // 6. Enregistrer l'offre pour l'utilisateur authentifié
    public function enregistrerOffre($offer_id)
    {
        // Vérifier si l'offre existe
        $offre = Offre::find($offer_id);
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }

        // Récupérer l'ID de l'utilisateur authentifié
        $userId = Auth::id();

        if (!$userId) {
            // Si l'utilisateur n'est pas authentifié, renvoyer une erreur
            return response()->json(['message' => 'Vous devez être connecté pour enregistrer une offre.'], 401);
        }

        // Vérifier si l'offre est déjà enregistrée par cet utilisateur
        $existingRecord = Offre::where('user_id', $userId)  // Vérifier la relation dans la table 'offres'
                                   ->where('id', $offer_id) // Utiliser `id` pour identifier l'offre
                                   ->first();

        if ($existingRecord) {
            // Si l'offre est déjà enregistrée, renvoyer une réponse indiquant que l'offre est déjà enregistrée
            return response()->json(['message' => 'Cette offre est déjà enregistrée !'], 400);
        }

        // Enregistrer l'offre aimée dans la table Offre (ajouter user_id)
        $offre->user_id = $userId;
        $offre->save();

        // Retourner une réponse de succès
        return response()->json(['message' => 'Offre enregistrée avec succès !'], 200);
    }
   public function getUserSelectedOffers()
{
    // Récupérer l'ID de l'utilisateur authentifié
    $userId = Auth::id();

    // Vérifier si l'utilisateur est authentifié
    if (!$userId) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    // Récupérer l'utilisateur en utilisant l'ID
    $user = User::find($userId); // Récupérer l'objet User

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    // Récupérer les offres enregistrées par cet utilisateur
    $offers = $user->offres;  // Utiliser la relation 'offres' de l'utilisateur

    return response()->json($offers);
}
public function deselectionnerOffre($offer_id)
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    // Chercher l'offre liée à cet utilisateur
    $offre = Offre::where('id', $offer_id)
                  ->where('user_id', $userId)
                  ->first();

    if (!$offre) {
        return response()->json(['message' => "Offre non trouvée ou non enregistrée par cet utilisateur."], 404);
    }

    // Désassocier l'offre de l'utilisateur (remet user_id à null ou autre valeur par défaut)
    $offre->user_id = null;
    $offre->save();

    return response()->json(['message' => 'Offre désélectionnée avec succès !'], 200);
}

}
