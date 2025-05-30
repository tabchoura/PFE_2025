<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        'offre_id',
        'cv_id',
        'user_id',  // Assurez-vous que user_id est bien dans les fillable
        'message',
        'statut',
 'status_ia',
        'date_entretien',
        'cv_embedding', 'match_score',
                'similarity_score', 

                

        'lien_visio'   ,

    ];
    protected $attributes = [
        'statut' => 'enattente',
    ];
    protected $casts = [
        'date_entretien' => 'datetime',
         'cv_embedding' => 'array',
        'match_score'  => 'float',
        
    ];

    // Relation avec l'offre
    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }

    // Relation avec le CV
    public function cv()
    {
        return $this->belongsTo(CV::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // app/Models/Candidature.php


}