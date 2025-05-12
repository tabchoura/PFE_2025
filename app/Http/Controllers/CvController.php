<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class CvController extends Controller
{
    /** Liste de tous les CV (admin ou utilisateur authentifié) */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $cvs = Cv::where('user_id', $userId)->get();
        return response()->json($cvs);
    }

    /** Création d’un CV */
    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email',
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
        $validatedata['user_id'] = $request->user()->id;

        try {
            // Créer le CV dans la base de données
            $cv = Cv::create($validatedata);

            // Créer le PDF avec les données du CV
            $pdfContent = $this->generatePdf($cv);

            // Sauvegarder le fichier PDF dans le dossier de stockage
            $pdfPath = 'cv_files/cv_' . $cv->id . '.pdf';
            file_put_contents(storage_path('app/' . $pdfPath), $pdfContent);

            // Enregistrer le chemin du fichier PDF dans la base de données
            $cv->update(['cv_path' => $pdfPath]);

            return response()->json($cv, 201); // Retourner le CV créé
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du CV', 'error' => $e->getMessage()], 500);
        }
    }

    /** Générer un fichier PDF à partir des données du CV */
    private function generatePdf($cv)
    {
        $dompdf = new Dompdf();

        // Options de DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);

        // Créer le contenu du PDF
        $html = view('cv_pdf_template', compact('cv'))->render(); // Récupérer un template Blade pour générer le HTML

        // Charger le contenu HTML dans DomPDF
        $dompdf->loadHtml($html);

        // (Optional) Définir la taille du papier
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Retourner le contenu du PDF
        return $dompdf->output();
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

    /** Mise à jour d’un CV */
    public function update(Request $request, $id)
    {
        $cv = Cv::find($id);
        if (!$cv) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }

        $validatedata = $request->validate([
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

        $cv->update($validatedata);
        return response()->json([
            'message' => 'CV mis à jour avec succès',
            'cv' => $cv->fresh(),
        ], 200);
    }

    /** Suppression d’un CV */
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
