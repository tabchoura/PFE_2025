<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;
    protected $fillable = [
       'type', 'description', 'salary', 'user_id'
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
