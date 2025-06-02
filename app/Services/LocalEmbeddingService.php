<?php

namespace App\Services;

use GuzzleHttp\Client;

class LocalEmbeddingService
{
    protected Client $http;
//bib GuzzleHttp bech namlou instanciation http ml classe client tkhalina nesnou des requets http bech najmou nbathou requete ll srvice ia 
    public function __construct()
    {
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


     //fonction hethi hia li tkhali service local yahki maa micor service ai bl endpoint yani yemchi ybath http fiha text l endpoint ytajjalou vecteur
    public function embedText(string $text): array
    {
        $resp = $this->http->post('/embed', [
            'json' => ['text' => $text],
        ]);

        $data = json_decode($resp->getBody(), associative: true);

        return $data['embedding'];
    }
}
