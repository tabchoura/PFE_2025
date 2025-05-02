<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Ajouter le facade Auth

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

    // 🔵 Voir les candidatures de l'utilisateur authentifié
    public function mescandidatures()
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();  // Utilisation correcte de Auth::user()
    
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Filtrer les candidatures associées à l'utilisateur connecté
        $candidatures = Candidature::where('user_id', $user->id)
                                   ->with(['offre'])  // Charger l'offre associée
                                   ->get();
    
        // Retourner les candidatures sous forme de réponse JSON
        return response()->json($candidatures);
    }
    
    // 🔵 Postuler à une offre (via API POST)
    // 
// public function store($id,Request $request)
//  {  
   
//     // Vérifier si l'utilisateur est authentifié
// //     $user = Auth::user(); 
// //     if (!$user) {
// //         return response()->json(['message' => 'Utilisateur non authentifié'], 401);
// //     }
// //     // Valider les données
// //     $validatedData = $request->validate([
// //         'offre_id' => 'required|exists:offres,id',
// //         'cv_id' => 'required|exists:cvs,id',
// //         'message' => 'nullable|string|max:1000',
// //         'statut' => 'nullable|string|max:1000',
// //     ]);
// //     // Créer la candidature
// //     $candidature = new Candidature();
// //     $candidature->offre_id = $validatedData['offre_id'];
// //     $candidature->cv_id = $validatedData['cv_id'];
// //     $candidature->message = $validatedData['message'];
// //     $candidature->statut = $validatedData['statut'];
// //     $candidature->user_id = $user->id; // Associer l'utilisateur authentifié à la candidature
// //     $candidature->save();

// //     return response()->json($candidature, 201); // Retourner la candidature après l'enregistrement
// }


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
