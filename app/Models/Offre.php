<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;
    protected $fillable = [
       'titre', 'description', 'salaire', 'details','embedding',

    ];

    protected $casts = [
  'embedding' => 'array',
];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

public function candidatures()
{
    return $this->hasMany(Candidature::class);
}
}
