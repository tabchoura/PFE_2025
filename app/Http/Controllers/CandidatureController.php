<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Offre;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LocalEmbeddingService;
use App\Jobs\EvaluateCvEmbedding;
use Illuminate\Support\Facades\Mail;
use App\Mail\EntretienPlanifie;
use Carbon\Carbon;


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
            'message' => 'Candidature acceptée.',
        ]);
    }

    public function refuse($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->update(['statut' => 'refuser']);

        return response()->json([
            'message' => 'Candidature refusée.',
        ]);
    }

    /* ------------------------------------------------------------------
     * 2. Postuler à une offre  (route : POST /offres/{id}/postuler)
     * ------------------------------------------------------------------ */
public function postuler(Request $request, $id)
{
    // Validation des données envoyées par le formulaire
    $request->validate([
        'cv_id'   => ['required', 'exists:cvs,id'],
        'message' => ['nullable', 'string', 'max:1000'],
    ]);

    // Récupérer le CV et l'offre
    $cv = Cv::find($request->cv_id);
    $offre = Offre::findOrFail($id);

    // Si le CV ou l'offre n'existe pas, retourner une erreur
    if (!$cv || !$offre) {
        return response()->json(['message' => 'CV ou Offre non trouvé.'], 404);
    }

    // Fusionner tous les champs du CV pour créer un seul texte
    $cvText = $cv->name . ' ' . $cv->email . ' ' . $cv->skills; // Exemple : Tu peux ajuster les champs en fonction de ton modèle

    // Appel au service d'Embedding pour générer un vecteur du texte du CV
    $embeddingService = app(LocalEmbeddingService::class);
    $cvEmbedding = $embeddingService->embedText($cvText);

    // Vérifier si l'embedding du CV est vide ou incorrect
    if (empty($cvEmbedding)) {
        return response()->json(['message' => 'Le texte extrait du CV est vide ou invalide.'], 400);
    }

    // Créer la candidature
    $candidature = Candidature::create([
        'offre_id' => $id,
        'user_id'  => auth()->id(),
        'cv_id'    => $request->cv_id,
        'message'  => $request->input('message', ''),
        'statut'   => $request->input('statut', 'enattente'),
    ]);

    // Sauvegarder l'embedding du CV dans la candidature
    $candidature->update([
        'cv_embedding' => json_encode($cvEmbedding), // Enregistrer l'embedding dans la base de données
    ]);

    // Dispatcher le job pour évaluer la similarité entre le CV et l'offre
    EvaluateCvEmbedding::dispatch($candidature);

    return response()->json([
        'message'     => '✅ Candidature envoyée avec succès.',
        'candidature' => $candidature,
    ], 201);
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
 public function show($id)
    {
        //with permet de charger offre et cv ensemble 
        $candidature = Candidature::with(['offre', 'cv', ])->find($id);

        if (! $candidature) {
            return response()->json(['message' => 'Candidature non trouvée'], 404);
        }

        return response()->json($candidature);
    }
    /* ---------------------
    ---------------------------------------------
     * 3. Envoyer un entretien à un candidat
     * ------------------------------------------------------------------ */

public function envoyerEntretien(Request $request, $id)
{
    // Validation de la date d'entretien au format d/m/Y
    $validated = $request->validate([
        'date_entretien' => ['required', 'date_format:d/m/Y'],  // Validation stricte au format jj/mm/aaaa
    ]);

    // Si la date est valide, elle sera déjà dans le bon format, vous pouvez la convertir si nécessaire
    // Exemple de conversion avec Carbon pour être sûr que le format est correct
    $validated['date_entretien'] = Carbon::createFromFormat('d/m/Y', $validated['date_entretien'])->format('Y-m-d');

    // 1. On récupère la candidature et on la met à jour
    $candidature = Candidature::with('cv')->findOrFail($id);
    $candidature->update([
        'date_entretien' => $validated['date_entretien'],
        'statut'         => 'entretien',
    ]);

    // 2. On récupère l'email dans le CV lié
    $recipientEmail = $candidature->cv->email;
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
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



    public function index()
    {
        return response()->json(
            Candidature::with(['offre', 'cv'])->get()
        );
    }

    /* ------------------------------------------------------------------
     * 6. Supprimer une candidature
     * ------------------------------------------------------------------ */
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
