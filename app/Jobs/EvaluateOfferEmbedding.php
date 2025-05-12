<?php
namespace App\Jobs;

use App\Models\Offre;
use App\Services\LocalEmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateOfferEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Offre $offre;

    public function __construct(Offre $offre)
    {
        $this->offre = $offre;
    }

    public function handle(): void
    {
        Log::info("Évaluation de l'offre ID : {$this->offre->id}");

        // Génération de l'**embedding** pour l'offre
        $embeddingService = app(LocalEmbeddingService::class);
        $embedding = $embeddingService->embedText($this->offre->description);

        // Enregistrer l'**embedding** dans la base de données
        $this->offre->update(['embedding' => json_encode($embedding)]);

        Log::info("Évaluation de l'offre terminée avec succès. ID : {$this->offre->id}");
    }
}
