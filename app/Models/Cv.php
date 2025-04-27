<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  // ✅ bon namespace
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;          // ✅ on “utilise” le trait
    protected $table = 'cvs';
    protected $guarded = [];

    protected $casts = [
        'experiences'           => 'array',
        'educations_formations' => 'array',
        'competences'           => 'array',
        'langues'               => 'array',
        'projets'               => 'array',
        // 'date_naissance'        => 'date',
    ];
}
