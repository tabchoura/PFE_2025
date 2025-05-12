<?php

namespace App\Jobs;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EvaluateCvMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Candidature $candidature;

    /**
     * Create a new job instance.
     */
    public function __construct(Candidature $candidature)
    {
        $this->candidature = $candidature;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Récupérer les embeddings du CV et de l'Offre
            $cvEmbedding = json_decode($this->candidature->cv_embedding, true);
            $offerEmbedding = json_decode($this->candidature->offre->embedding ?? '[]', true);

            // Vérification des embeddings
            if (empty($cvEmbedding) || empty($offerEmbedding)) {
                throw new \Exception("L'un des embeddings (CV ou offre) est vide.");
            }

            // Calcul de la similarité cosinus entre les deux embeddings
            $score = $this->compareEmbeddings($cvEmbedding, $offerEmbedding);

            // Déterminer le statut de la candidature en fonction du score de similarité
            $status = $score > 0.7 ? 'accepted' : 'rejected';

            // Mettre à jour le statut de la candidature
            $this->candidature->update([
                'similarity_score' => $score,
                'status_ia' => $status,
            ]);
        } catch (\Exception $e) {
            // Gérer l'erreur (par exemple, loguer l'erreur)
            throw new \Exception("Erreur lors de la comparaison du CV et de l'offre : " . $e->getMessage());
        }
    }

    /**
     * Calcule la similarité cosinus entre deux vecteurs.
     */
    private function compareEmbeddings(array $cvEmbedding, array $offerEmbedding): float
    {
        $dotProduct = 0.0;
        $cvNorm = 0.0;
        $offerNorm = 0.0;

        $length = min(count($cvEmbedding), count($offerEmbedding));

        for ($i = 0; $i < $length; $i++) {
            $dotProduct += $cvEmbedding[$i] * $offerEmbedding[$i];
            $cvNorm += $cvEmbedding[$i] ** 2;
            $offerNorm += $offerEmbedding[$i] ** 2;
        }

        // Calcul de la similarité cosinus
        return ($cvNorm && $offerNorm) ? $dotProduct / (sqrt($cvNorm) * sqrt($offerNorm)) : 0.0;
    }
}
