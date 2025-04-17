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
        ]);
        $user = User::create([

            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'cin' => $request->cin,
            'phone' => $request->numtel,
            'datedepot' => now(),
            'age' => $request->date, // ✅ champ cohérent
            'lieudenaissance' => $request->lieu  // ✅ champ cohérent
        ]);

        $token = $user->createToken($user->role . '_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
        
    }


    // Connexion d'un utilisateur
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        $token = $user->createToken($user->role . '_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    // Déconnexion de l'utilisateur
    public function logout(Request $request)
    {
        // Récupérer l'utilisateur actuel
        $user = $request->user();
    
        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }
    
        // Supprimer les tokens de l'utilisateur pour le déconnecter
        $user->tokens()->delete();
    
        return response()->json(['message' => 'Déconnexion réussie']);
    }
    
}    
