<?php
namespace App\Jobs;

use App\Models\Candidature;
use App\Models\Cv;
use App\Services\LocalEmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

class EvaluateCvEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Candidature $candidature;
    protected Cv $cv;

    public function __construct(Candidature $candidature)
    {
        $this->candidature = $candidature;
        $this->cv = $candidature->cv; 
    }

    public function handle(): void
    {
        Log::info("Début de l'évaluation du CV pour la candidature ID : {$this->candidature->id}");
        
        $cvPath = storage_path('app/' . $this->cv->cv_path);
        
        if (!file_exists($cvPath)) {
            Log::error("Le fichier CV n'existe pas à l'emplacement : $cvPath");
            throw new \Exception("Le fichier CV n'existe pas.");
        }

        $text = $this->extractTextFromFile($cvPath);

        if (empty($text)) {
            Log::error("Le texte extrait du CV est vide.");
            throw new \Exception("Le texte extrait du CV est vide.");
        }
//app fonction laravel bech ynedi ssrvice local 
        $embeddingService = app(LocalEmbeddingService::class);
        $cvEmbedding = $embeddingService->embedText($text);

        if (!is_array($cvEmbedding)) {
            Log::error("L'embedding du CV est invalide.");
            throw new \Exception("L'embedding du CV est invalide.");
        }

        $offerEmbedding = $embeddingService->embedText($this->candidature->offre->description);

        $similarityScore = $this->calculateSimilarity($cvEmbedding, $offerEmbedding);
$statusIa = $similarityScore >= 0.6 ? 'accepted' : 'rejected';  

        $this->candidature->update([
        'similarity_score' => $similarityScore,
        'status_ia' => $statusIa,  
    ]);

    Log::info("Évaluation du CV terminée. Similarité : $similarityScore, Status IA : $statusIa");
    }

      private function extractTextFromFile($filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            if ($ext === 'pdf') {
                return $this->extractTextFromPdf($filePath);
            }
           else {
                throw new \Exception("Format de fichier non pris en charge : $ext");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'extraction du texte du fichier CV : " . $e->getMessage());
            throw new \Exception("Erreur lors de l'extraction du texte du fichier CV.");
        }
    }

    private function extractTextFromPdf($path): string
    {
        //parser instance men bib Parser smlot parserpdf capable bech yakra apartir men pdf w ykharj text 
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }
 
    private function calculateSimilarity(array $embedding1, array $embedding2): float
    {
        // Produit scalaire
        $dotProduct = array_sum(array_map(function ($x, $y) {
            return $x * $y;
        }, $embedding1, $embedding2));

        // Norme des deux vecteurs
        $magnitude1 = sqrt(array_sum(array_map(function ($x) {
            return $x * $x;
        }, $embedding1)));

        $magnitude2 = sqrt(array_sum(array_map(function ($x) {
            return $x * $x;
        }, $embedding2)));

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

   
  

   
   
}