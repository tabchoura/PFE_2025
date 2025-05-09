<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\EntretienPlanifie;
use Illuminate\Support\Facades\Mail;

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

    public function envoyerEntretien(Request $request, $id)
    {
        $validated = $request->validate([
            'date_entretien' => ['required','date'],
        ]);

        // 1. On récupère la candidature et on la met à jour
        $candidature = Candidature::with('cv')->findOrFail($id);
        $candidature->update([
            'date_entretien' => $validated['date_entretien'],
            'statut'         => 'entretien',
        ]);

        // 2. On récupère l'email dans le CV lié
        $recipientEmail = $candidature->cv->email;
        if (! filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'message' => 'L’adresse e-mail du candidat est invalide.',
            ], 422);
        }

        // 3. On envoie l’email à cet e-mail
        Mail::to($recipientEmail)
            ->send(new EntretienPlanifie($candidature));

        return response()->json([
            'message'        => 'Date d’entretien enregistrée et email envoyé à '.$recipientEmail,
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
