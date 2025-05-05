<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Ajouter le facade Auth

class CandidatureController extends Controller
{ public function accept($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->statut = 'accepter';
        $candidature->save();

        return response()->json([
            'message'     => 'Candidature acceptée.',
            'candidature' => $candidature,
        ], 200);
    }

    /**
     * Refuse une candidature.
     */
    public function refuse($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->statut = 'refuser';
        $candidature->save();

        return response()->json([
            'message'     => 'Candidature refusée.',
            'candidature' => $candidature,
        ], 200);
    }

    public function postuler(Request $request, $id)
{
    // Validation des données
    $request->validate([
        'cv_id' => 'required|exists:cvs,id', // Le CV doit exister dans la table `cvs`
        // 'message' => 'nullable|string|max:1000', // Le message est facultatif
    ]);

    

    // Récupération de l'offre avec l'ID
    // $user_id=auth()->id();    
    
    // Créer une nouvelle candidature pour l'offre
    $candidature = new Candidature();
    $candidature->message  = $request   ['message'] ?? null;    // ← ici !
    $candidature->offre_id = $id; // Lier l'offre à la candidature
    $candidature->cv_id = $request->cv_id; // Lier le CV à la candidature
  $candidature->user_id =auth()->id();   
$candidature->statut = $request->input('statut', 'enattente');
$candidature->save(); // Sauvegarder la candidature dans la base de données

    return response()->json([
        'message' => '✅ Candidature envoyée avec succès.'
    ]);
}
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
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
    
        $candidatures = Candidature::where('user_id', $user->id)
                                   ->with(['offre', 'cv']) // ✅ ajoute la relation vers le CV
                                   ->get();
    
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
