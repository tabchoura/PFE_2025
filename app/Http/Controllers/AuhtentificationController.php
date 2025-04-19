<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuhtentificationController extends Controller
{
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
            'type' => $user->role
        ]);

    }

}
