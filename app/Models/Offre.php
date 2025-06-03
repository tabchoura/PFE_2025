<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;
     protected $guarded = [];


    protected $casts = [
  'embedding' => 'array',
];

public function candidatures()
{
    return $this->hasMany(Candidature::class);
}
}
