<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Inscription d'un utilisa+teur (admin, recruteur ou candidat)
    public function register(Request $request)
    {   

        $request->validate([
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:candidat,recruteur,admin',
            'date_naissance'=>'date',
            'nomentreprise'=>'string',
            'lieu'=>'string',  // Correction ici
            'siteweb'=>'string',   // Correction ici
            'cin' =>'required|string',   // Correction ici
            'phone' => 'string',   // Correction ici
            'departement'=>'string',
            'description'=>'string',
            


        ]);
        $user = User::create([



            'siteweb'=>$request->siteweb,
            'departement'=>$request->departement,
            'description'=>$request->description,
            'nom' => $request->nom,
            'prenom' => $request->prenom,

            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'cin' => $request->cin,
            'phone' => $request->numtel,
            'datedepot' => now(),
            'date_naissance' => $request->date_naissance,
            'lieudenaissance' => $request->lieu ,// ✅ champ cohérent
        
        ]);

     return response()->json([
    'message' => 'Inscription réussie ✅',
    'user' => $user,
    'type' => $user->role,
]);

        

        
    }


    // Connexion d'un utilisateur
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
    
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Bad credentials'], 401);
        }
    
        $user  = Auth::user();             // connecté
        $token = $user->createToken('spa')->plainTextToken;   // <-- token Sanctum
    
        return response()->json([
            'user'  => $user,
            'token' => $token,              // on l’envoie au front
            'type'  => $user->role,
        ]);
    }
    

    // Déconnexion de l'utilisateur
    public function logout(Request $request)
    {
        Auth::logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
    
    
}    
