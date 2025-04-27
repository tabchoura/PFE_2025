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
        'message',
        'statut'
    ];

    // 🔵 Relation : une candidature appartient à une offre
    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }

    // 🔵 Relation : une candidature appartient à un CV
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
