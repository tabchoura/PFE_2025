<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

  
    protected $guarded = [];

    
    
    
 
    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function offres()
{
    return $this->hasMany(Offre::class);
}
public function entretiens()
{
    return $this->hasMany(Entretien::class); // Un utilisateur peut avoir plusieurs entretiens
}
public function cvs()
    {
        return $this->hasMany(Cv::class); // Un utilisateur peut avoir plusieurs CVs
    }
    public function candidatures()
    {
        return $this->hasMany(Candidature::class);  // Un utilisateur a plusieurs candidatures
    }
}
