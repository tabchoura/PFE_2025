<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;

class CandidatureController extends Controller
{
    // 🔵 Voir toutes les candidatures (optionnel)
    public function index()
    {
        return response()->json(Candidature::with(['offre', 'cv'])->get());
    }

    // 🔵 Voir une seule candidature
    public function show($id)
    {
        $candidature = Candidature::with(['offre', 'cv'])->find($id);

        if (!$candidature) {
            return response()->json(['message' => 'Candidature non trouvée'], 404);
        }

        return response()->json($candidature);
    }

    // 🔵 Postuler à une offre (via API POST)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'offre_id' => 'required|exists:offres,id',
            'cv_id' => 'required|exists:cvs,id',
            'message' => 'nullable|string|max:1000',
            'statut'=>'nullable|string|max:1000',
            
        ]);

        $candidature = Candidature::create($validatedData);

        return response()->json([
            'message' => 'Candidature enregistrée avec succès!',
            'candidature' => $candidature
        ], 201);
    }

    // 🔵 Supprimer une candidature
    public function destroy($id)
    {
        $candidature = Candidature::find($id);

        if (!$candidature) {
            return response()->json(['message' => 'Candidature non trouvée'], 404);
        }

        $candidature->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès']);
    }
}
