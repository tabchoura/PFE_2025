<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    /* ------------------------------------------------------------------
     * 1. Changer le statut (accepter / refuser)
     * ------------------------------------------------------------------ */
    public function accept($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->update(['statut' => 'accepter']);

        return response()->json([
            'message'     => 'Candidature acceptée.',
        ]);
    }

    public function refuse($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->update(['statut' => 'refuser']);

        return response()->json([
            'message'     => 'Candidature refusée.',
        ]);
    }

    /* ------------------------------------------------------------------
     * 2. Postuler à une offre  (route : POST /offres/{id}/postuler)
     * ------------------------------------------------------------------ */
    public function postuler(Request $request, $id)
    {
        $request->validate([
            'cv_id'   => ['required', 'exists:cvs,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $candidature = Candidature::create([
            'offre_id' => $id,
            'cv_id'    => $request->cv_id,
            'user_id'  => auth()->id(),
            'message'  => $request->input('message'),
            'statut'   => $request->input('statut', 'enattente'),
        ]);

        return response()->json([
            'message'     => '✅ Candidature envoyée avec succès.',
        ], 201);
    }

    /**
 * Planifie et envoie la date d’entretien pour une candidature.
 */public function envoyerEntretien(Request $request, $id)
{
    // Au lieu de « date_format », on utilise simplement « date »
    $validated = $request->validate([
        'date_entretien' => ['required', 'date'],  
    ]);

    $candidature = Candidature::findOrFail($id);
    $candidature->update([
        'date_entretien' => $validated['date_entretien'],
        'statut'         => 'entretien',
    ]);

    return response()->json([
        'message'        => 'Date d’entretien enregistrée.',
        'date_entretien' => $candidature->date_entretien,
        'candidature'    => $candidature,
    ], 200);
}



    /* ------------------------------------------------------------------
     * 3. Lister et afficher
     * ------------------------------------------------------------------ */
    public function index()
    {
        return response()->json(
            Candidature::with(['offre', 'cv', ])->get()
        );
    }

    public function show($id)
    {
        $candidature = Candidature::with(['offre', 'cv', ])->find($id);

        if (! $candidature) {
            return response()->json(['message' => 'Candidature non trouvée'], 404);
        }

        return response()->json($candidature);
    }

    public function mescandidatures()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $candidatures = Candidature::where('user_id', $user->id)
            ->with(['offre', 'cv', ])
            ->get();

        return response()->json($candidatures);
    }

    /* ------------------------------------------------------------------
     * 4. Supprimer
     * ------------------------------------------------------------------ */
    public function destroy($id)
    {
        $candidature = Candidature::find($id);

        if (! $candidature) {
            return response()->json(['message' => 'Candidature non trouvée'], 404);
        }

        $candidature->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès']);
    }
}
