<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;
    protected $fillable = [
       'titre', 'description', 'salaire', 'details'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function entretiens()
{
    return $this->hasMany(Entretien::class);
}
}
