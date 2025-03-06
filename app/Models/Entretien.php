<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'statut', 'date_entretien', 'time', 'user_id'
    ];
    protected $attributes = [
        'time' => '00:00:00',
    ];
    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }
    public function user()
{
    return $this->belongsTo(User::class); // Un entretien appartient à un utilisateur
}
}
