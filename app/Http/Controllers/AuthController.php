<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Inscription d'un utilisa+teur ( recruteur ou candidat)
    public function register(Request $request)
    {
       $validated= $request->validate([
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:candidat,recruteur',
            'date_naissance' => 'date',
            'nomsociete' => 'nullable|string',
            'lieudenaissance' => 'string',
            'localisation'=>'string',
            'siteweb' => 'string',
            'cin' => 'required|string',
            'phone' => 'string',
        ]);

    $user = User::create($validated);

      
    }



    // Connexion d'un utilisateur
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
    // attempt :  essaie de connecter l’utilisateur avec les identifiants fournis.
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Bad credentials'], 401);
        }

        $user  = Auth::user();             // connecté
        $token = $user->createToken('spa')->plainTextToken;   // <-- token Sanctum

        return response()->json([
            'user'  => $user,
            'token' => $token,
            'type'  => $user->role,
        ]);
    }


    // Déconnexion de l'utilisateur
    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Déconnexion réussie']);
    }

}
