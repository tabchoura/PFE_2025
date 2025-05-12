<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Services\LocalEmbeddingService;
use Illuminate\Http\Request;
use App\Jobs\EvaluateOfferEmbedding;


class OfferController extends Controller
{
    // 1. Récupère toutes les offres
    public function index()
    {
        return response()->json(Offre::all());
    }

    // 2. Crée une nouvelle offre
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'salaire'     => 'nullable|string',
            'details'     => 'nullable|string|max:255',
        ]);

       $offre = Offre::create($validated);
       // // Appeler le job pour évaluer l'offre (générer l'**embedding**)
    EvaluateOfferEmbedding::dispatch($offre);

        // Génération de l'embedding sur la description
        $vector = app(LocalEmbeddingService::class)
                     ->embedText($offre->description);

        // Mise à jour de l'offre avec l'embedding
        $offre->update(['embedding' => json_encode($vector)]); // Stockage sous forme JSON

        // Retourne l'offre complète (avec embedding)
        return response()->json($offre, 201);
    }

    // 3. Met à jour une offre existante
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
}
