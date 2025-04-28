<?php
namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;

class CvController extends Controller
{
    /** Liste de tous les CV (admin ou utilisateur authentifié) */
    public function index(Request $request)
    {
        $userId = $request->user()->id; // Récupérer l'ID de l'utilisateur connecté
        $cvs = Cv::where('user_id', $userId)->get(); // Récupérer les CV de cet utilisateur
        return response()->json($cvs);
    }

    /** Création d’un CV */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:cvs,email',
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'presentation' => 'nullable|string',
            'experiences' => 'nullable|array',
            'educations_formations' => 'nullable|array',
            'competences' => 'nullable|array',
            'langues' => 'nullable|array',
            'projets' => 'nullable|array',
            'image' => 'nullable|url', 
        ]);

        // Ajouter le user_id avant de créer le CV
        $data['user_id'] = $request->user()->id;

        try {
            // Créer le CV avec le user_id
            $cv = Cv::create($data);
            return response()->json($cv, 201); // Retourner le CV créé
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du CV', 'error' => $e->getMessage()], 500);
        }
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
        if (!$cv) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:cvs,email,' . $cv->id,
            'date_naissance' => 'sometimes|date',
            'adresse' => 'sometimes|string',
            'presentation' => 'sometimes|string',
            'experiences' => 'sometimes|array',
            'educations_formations' => 'sometimes|array',
            'competences' => 'sometimes|array',
            'langues' => 'sometimes|array',
            'projets' => 'sometimes|array',
            'image' => 'nullable|url',
        ]);

        $cv->update($data);
        return response()->json([
            'message' => 'CV mis à jour avec succès',
            'cv' => $cv->fresh(),
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
