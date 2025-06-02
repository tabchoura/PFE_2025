<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class CvController extends Controller
{

    public function store(Request $request)
    {


        $validated = $request->validate([
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
        $validated['user_id'] = $request->user()->id;

        try {
            // Créer le CV dans la base de données
            $cv = Cv::create($validated);

            // Créer le PDF avec les données du CV
            $pdfContent = $this->generatePdf($cv);

            // Sauvegarder le fichier PDF dans le dossier de stockage
            $pdfPath = 'cv_files/cv_' . $cv->id . '.pdf';
            // emchi ll storage/app odkhl ll pdf path hotlou lcontenue mteeou generer
            file_put_contents(storage_path('app/' . $pdfPath), $pdfContent);

            // Enregistrer le chemin du fichier PDF dans la base de données
            $cv->update(['cv_path' => $pdfPath]);

            return response()->json($cv, 201); // Retourner le CV créé
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du CV', 'error' => $e->getMessage()], 500);
        }
    }
        public function index(Request $request)
    {
        $userId = $request->user()->id;
        $cvs = Cv::where('user_id', $userId)->get();
        return response()->json($cvs);
    }


    /** Générer un fichier PDF à partir des données du CV */
    private function generatePdf($cv)
    {
        $dompdf = new Dompdf();

        // Options de DomPDF
        $options = new Options();
        //active le support du HTML5 moderne
        $options->set('isHtml5ParserEnabled', true);
        //: autorise du PHP dans le code HTML (utile avec Blade si tu fais des conditions, des boucles, etc.)
        $options->set('isPhpEnabled', true);
        //application  des options 
        $dompdf->setOptions($options);

        // Créer le contenu du PDF
        //recuperer ml bladde el template wbaed compact bch yesna kif tableau associative yaani cv : $cv eli adineha fl blade bech ynajm tsir recuperation , wmbaed render bech ygenirilek el blade ila html puur 
        $html = view('cv_pdf_template', compact('cv'))->render(); // Récupérer un template Blade pour générer le HTML

        // Charger le contenu HTML dans DomPDF
        //loadthtml tebaa bib dompdf charge html dans dompdf
        $dompdf->loadHtml($html);

        // (Optional) Définir la taille du papier
        $dompdf->setPaper('A4', 'portrait');

//convertie html en pdf 
        $dompdf->render();

        // Retourner le contenu du PDF
        return $dompdf->output();
    }

    public function show($id)
    {
        $cv = Cv::find($id);
        if (!$cv) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }
        return response()->json($cv);
    }

    public function update(Request $request, $id)
    {
        $cv = Cv::find($id);
        if (!$cv) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }

        $validated = $request->validate([
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

$cv->update($validated)   ;
   return response()->json([
            'message' => 'CV mis à jour avec succès',
            'cv' => $cv->fresh(),
        ], 200);
    }

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
