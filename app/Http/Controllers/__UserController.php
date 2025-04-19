<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
   public function index(){
        return response()->json(User::all());
    }
    
public function store(Request $request){
    $validatedData = $request->validate([
        'name'          => 'required|string|max:255',
        'last_name'     => 'required|string|max:255',
        'age'           => 'required|integer|min:18',
        'lieudenaissance'=> 'required|integer|min:18',
   
        'phone'         => 'required|string|max:20',
        'cv'            => 'required|string', 
        'cover_letter'  => 'required|string',
        'datedepot'     => 'nullable|date',
        'datevalidation'=> 'required|date',
        'dateentretien' => 'required|date',
        'cin'           => 'required|string|max:20',
        'poste'         => 'required|string|max:255',
        'nb_offres'     => 'required|integer|min:0',
        'nbvalidation'  => 'required|integer|min:0',
        'email'         => 'required|email|unique:users,email',
        'password'      => 'required|string|min:6',
    ]);
    

    $user=User::create([
        'name'          => $validatedData['name'],
        'last_name'     => $validatedData['last_name'],
        'date_naissance'           => $validatedData['age'],
        'phone'         => $validatedData['phone'],
        'lieudenaissance' => $validatedData['lieudenaissance'],
        'cv'            => $validatedData['cv'], 
        'cover_letter'  => $validatedData['cover_letter'],
        'datedepot'     => $validatedData['datedepot'],
        'datevalidation'=> $validatedData['datevalidation'],
        'dateentretien' => $validatedData['dateentretien'],
        'cin'           => $validatedData['cin'],
        'poste'         => $validatedData['poste'],
        'nb_offres'     => $validatedData['nb_offres'],
        'nbvalidation'  => $validatedData['nbvalidation'],
        'email'         => $validatedData['email'],
        'password'      => bcrypt($validatedData['password']),

    ]);
    return response()->json($user, 201);
}
public function update(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $validatedData = $request->validate([
        'name'          => 'sometimes|string|max:255',
        'last_name'     => 'sometimes|string|max:255',
        'age'           => 'sometimes|integer|min:18',
        'phone'         => 'sometimes|string|max:20',
        'cv'            => 'sometimes|string',
        'lieudenaissance'=> 'sometimes|string',

        'cover_letter'  => 'sometimes|string',
        'datedepot'     => 'nullable|date',
        'datevalidation'=> 'sometimes|date',
        'dateentretien' => 'sometimes|date',
        'cin'           => 'sometimes|string|max:20',
        'poste'         => 'sometimes|string|max:255',
        'nb_offres'     => 'sometimes|integer|min:0',
        'nbvalidation'  => 'sometimes|integer|min:0',
        'email'         => 'sometimes|email|unique:users,email,' . $id,
        'password'      => 'sometimes|string|min:6',
    ]);

    // Si un mot de passe est fourni, le hacher
    if (isset($validatedData['password'])) {
        $validatedData['password'] = bcrypt($validatedData['password']);
    }

    $user->update($validatedData);
    return response()->json($user);
}
public function destroy($id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $user->delete();
    return response()->json(data: ['message' => 'Utilisateur supprimé avec succès']);
}
const token = session.token;

public function myProfile() {

    $user = auth()->user(); // Vérifie si l'utilisateur est authentifié

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);  // Si l'utilisateur n'est pas trouvé, renvoie une erreur
    }

    return response()->json($user);  // Retourne les données utilisateur en JSON
}


    
}
