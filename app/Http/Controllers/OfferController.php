<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    // Récupère toutes les offres
    public function index()
    {
        return response()->json(Offre::all());
    }

    // Crée une nouvelle offre
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
         //   'type'        => 'nullable|string|max:255',
            'titre'       => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'salaire'     => 'nullable|string',
            'details'     => 'nullable|string',

        ]);

        // Création de l'offre
        $offre = Offre::create([
           // 'type'        => $validatedData['type'],
            'titre'       => $validatedData['titre'],
            'description' => $validatedData['description'],
            'salaire'     => $validatedData['salaire'],
            'details'     => $validatedData['details'],

        ]);

        // Retourne la nouvelle offre créée
        return response()->json($offre, 201);
    }

    // Met à jour une offre existante
    public function update(Request $request, $id)
    {
        // Récupère l'offre par son ID
        $offre = Offre::find($id);
    
        // Si l'offre n'est pas trouvée, retourne une erreur 404
        if (!$offre) {
            return response()->json(['message' => 'Offre de travail non trouvée'], 404);
        }
    
        // Validation des données pour la mise à jour
        $validatedData = $request->validate([
           // 'type'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'salaire'     => 'nullable|string|max:255',
            'titre'       => 'nullable|string|max:255',  
            'titre'       => 'nullable|string|max:255', 
            'details'       => 'nullable|string|max:255', 
        ]);
    
        // Si les données sont valides, mettre à jour l'offre avec les nouvelles données
        $offre->update($validatedData);
    
        // Retourne une réponse de succès avec l'offre mise à jour
        return response()->json([
            'message' => 'Offre mise à jour avec succès.',
            'offre' => $offre
        ], 200);
    }
    

    // Supprime une offre
    public function destroy($id)
    {
        $offre = Offre::find($id);
    
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }
    
        $offre->delete();
        return response()->json(['message' => 'Offre supprimée avec succès']);
    }
    // Dans le contrôleur Laravel
    public function addDetails(Request $request, $id)
    {
        // Récupérer l'offre par son ID
        $offer = Offre::findOrFail($id);
    
        // Ajouter les détails supplémentaires
        $offer->details = $request->input('details');
        $offer->save(); // Sauvegarder les modifications
    
        return response()->json(['message' => 'Détails ajoutés avec succès'], 200);
    }
    

    
}
