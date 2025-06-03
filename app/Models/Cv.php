<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $table = 'cvs';  
    protected $guarded = [];

    protected $casts = [
        'experiences' => 'array',
        'educations_formations' => 'array',
        'competences' => 'array',
        'langues' => 'array',
        'projets' => 'array',
        // 'date_naissance' => 'date',
    ];

  
    public function candidatures()
    {
        return $this->hasMany(Candidature::class);  

    }
    public function user()
    {
        return $this->belongsTo(User::class); 
    }
}
