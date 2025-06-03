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
use Illuminate\Support\Facades\Log;



class CandidatureController extends Controller
{
    /* ------------------------------------------------------------------
     * 1. Changer le statut (accepter / refuser)
    //  * ------------------------------------------------------------------ */
    // public function accept($id)
    // {
    //     $candidature = Candidature::findOrFail($id);
    //     $candidature->update(['statut' => 'accepter']);

    //     return response()->json([
    //         'message' => 'Candidature acceptée.',
    //     ]);
    // }

    // public function refuse($id)
    // {
    //     $candidature = Candidature::findOrFail($id);
    //     $candidature->update(['statut' => 'refuser']);

    //     return response()->json([
    //         'message' => 'Candidature refusée.',
    //     ]);
    // }

public function postuler(Request $request, $id)
{
    $request->validate([
        'cv_id'   => ['required', 'exists:cvs,id'],
        'message' => ['nullable', 'string', 'max:1000'],
    ]);

    $cv = Cv::find($request->cv_id);
    $offre = Offre::findOrFail($id);

    if (!$cv || !$offre) {
        return response()->json(['message' => 'CV ou Offre non trouvé.'], 404);
    }

    $candidature = Candidature::create([
        'offre_id' => $id,
        'user_id'  => auth()->id(),
        'cv_id'    => $request->cv_id,
        'message'  => $request->input('message', ''),
        // 'statut'   => $request->input('statut', 'enattente'),
    ]);

    //dispatch manehaa Lance le job EvaluateCvEmbedding avec la candidature donnée lancement automatique khater def queue sync l methode handle automatiquement ynedilha baed kadkad 
    //yani dipsatch houa instanciation mtee job + activation handle hekka chnw yaaml bethabt  
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
        $candidature = Candidature::with(['offre', 'cv', ])->find($id);

        if (! $candidature) {
            return response()->json(['message' => 'Candidature non trouvée'], 404);
        }

        return response()->json($candidature);
    }
  
    public function index()
    {
        return response()->json(
            Candidature::with(['offre', 'cv'])->get()
        );
    }

public function envoyerEntretien(Request $request, $id)
{
    Log::info('envoyerEntretien payload', $request->all());

  
        $validated = $request->validate([
            'date_entretien' => ['required', 'date_format:d/m/Y H:i'],
            'lien_visio'     => ['required', 'url'],
        ]);
        
        Log::info('Validation OK', $validated);
        $dt = Carbon::createFromFormat('d/m/Y H:i', $validated['date_entretien']);
        Log::info('Carbon parsed', ['datetime' => $dt->toDateTimeString()]);

        $candidature = Candidature::findOrFail($id);
        $candidature->update([
            'date_entretien' => $dt->format('Y-m-d H:i:s'),
            'lien_visio'     => $validated['lien_visio'],
            'statut'         => 'entretien',
        ]);

        if ($candidature->cv && $candidature->cv->email) {
            Mail::to($candidature->cv->email)
                ->send(new EntretienPlanifie($candidature));
        } else {
            Log::warning("La candidature $id n'a pas de CV ou d'email valide pour l'envoi du mail.");
        }

        return response()->json([
            'message'     => 'Entretien planifié et email envoyé.',
            'candidature' => $candidature,
        ], 200);

        
  
}

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