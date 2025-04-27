<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;

class CvController extends Controller
{
    /** Liste de tous les CV (admin) */
    public function index()
    {
        return response()->json(Cv::all());
    }

    /** Création d’un CV */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'        => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            'email'      => 'nullable|email|unique:cvs,email',
            'date_naissance'      => 'nullable|date',
            'adresse'    => 'nullable|string',
            'presentation'            => 'nullable|string',
            'experiences'             => 'nullable|array',
            'educations_formations'   => 'nullable|array',
            'competences'             => 'nullable|array',
            'langues'                 => 'nullable|array',
            'projets'                 => 'nullable|array',
            'image'      => 'nullable|url', 
        ]);



        $cv = Cv::create($data);          // grâce au $fillable dans le modèle
        return response()->json($cv, 201); // <-- contient $cv->id
    }

    /** Affichage d’un CV précis */
    public function show($id)
    {
        $cv = Cv::find($id);
        if (!$cv) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }
        return response()->json($cv);
    }

    /** Mise à jour */
   public function update(Request $request, $id)
{
    $cv = Cv::find($id);
    if (! $cv) {
        return response()->json(['message' => 'CV non trouvé'], 404);
    }

    $data = $request->validate([
        'nom'                     => 'sometimes|string|max:255',
        'prenom'                  => 'sometimes|string|max:255',
        'email'                   => 'sometimes|email|unique:cvs,email,' . $cv->id,
        'date_naissance'          => 'sometimes|date',
        'adresse'                 => 'sometimes|string',
        'presentation'            => 'sometimes|string',
        'experiences'             => 'sometimes|array',
        'experiences.*'           => 'string',
        'educations_formations'   => 'sometimes|array',
        'educations_formations.*' => 'string',
        'competences'             => 'sometimes|array',
        'competences.*'           => 'string',
        'langues'                 => 'sometimes|array',
        'langues.*'               => 'string',
        'projets'                 => 'sometimes|array',
        'projets.*'               => 'string',
        'image'                   => 'nullable|url',
        // 'photo'                => 'sometimes|file|image|max:2048', // si upload
    ]);

    $cv->update($data);

    return response()->json([
        'message' => 'CV mis à jour avec succès',
        'cv'      => $cv->fresh(),
    ], 200);
}

    /** Suppression */
    public function destroy($id)
    {
        $cv = Cv::find($id);
        if (!$cv) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }
        $cv->delete();
        return response()->json(['message' => 'CV supprimé avec succès']);
    }
}
