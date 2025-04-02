<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Enregistrement d'un utilisateur (candidat, recruteur, admin)
    public function register(Request $request)
    {
        // Validation des données
        $request->validate([
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:candidat,recruteur,admin',
        ]);
        

        // Créer un nouvel utilisateur
        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Récupérer le rôle
        ]);

        // Créer le token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retourner la réponse
        return response()->json(['token' => $token, 'user' => $user]);
    }

    // Connexion d'un utilisateur (avec gestion des rôles)
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Chercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Vérification des informations d'identification
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Créer un token selon le rôle
        $token = $user->createToken($user->role.'_token')->plainTextToken;

        // Retourner la réponse avec le token et l'utilisateur
        return response()->json(['token' => $token, 'user' => $user]);
    }

    // Déconnexion (logout) de l'utilisateur
    public function logout(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'No user authenticated'], 401); // Code 401 pour non authentifié
        }
    
        // Supprimer tous les tokens de l'utilisateur
        $user->tokens->each(function ($token) {
            $token->delete();
        });
    
        // Retourner une réponse de succès
        return response()->json(['message' => 'Logged out successfully']);
    }
    
}
