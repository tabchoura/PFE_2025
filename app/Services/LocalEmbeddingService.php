<?php

namespace App\Services;

use GuzzleHttp\Client;

class LocalEmbeddingService
{
    protected Client $http;

    public function __construct()
    {
        // On instancie correctement le client Guzzle ici
        $this->http = new Client([
            'base_uri' => env('AI_SERVICE_URL', 'http://127.0.0.1:8001'),
            'timeout'  => 10,
        ]);
    }

    /**
     * Envoie le texte au micro-service Python et retourne l'embedding.
     *
     * @param string $text
     * @return array
     */
    public function embedText(string $text): array
    {
        $resp = $this->http->post('/embed', [
            'json' => ['text' => $text],
        ]);

        $data = json_decode($resp->getBody(), true);

        return $data['embedding'];
    }
}
