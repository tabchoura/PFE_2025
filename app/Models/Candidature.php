<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'statut' => 'enattente',
    ];
    protected $casts = [
        'date_entretien' => 'datetime',
         'cv_embedding' => 'array',
        'match_score'  => 'float',
        
    ];

    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }

    public function cv()
    {
        return $this->belongsTo(CV::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    


}