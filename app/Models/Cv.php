<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    // Définir le nom de la table si ce n'est pas conventionnel
    protected $table = 'cvs';  // Assurez-vous que la table est bien nommée 'cvs' dans votre base de données

    // Assurez-vous que les champs protégés sont corrects
    protected $guarded = [];  // Vous pouvez aussi utiliser $fillable pour les champs autorisés à être massivement assignés

    protected $casts = [
        'experiences' => 'array',
        'educations_formations' => 'array',
        'competences' => 'array',
        'langues' => 'array',
        'projets' => 'array',
        // 'date_naissance' => 'date', // Si vous avez une colonne date, vous pouvez décommenter
    ];

    /**
     * Un CV peut avoir plusieurs candidatures.
     */
    public function candidatures()
    {
        return $this->hasMany(Candidature::class);  // Une relation hasMany avec le modèle Candidature
    }

    /**
     * Un CV appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);  // Relation inverse avec le modèle User
    }
}
