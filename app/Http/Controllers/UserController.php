<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{

    public function myProfile() {

        $user = auth()->user(); // Vérifie si l'utilisateur est authentifié
    
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    
        return response()->json($user);
    }
    

    
    // public function index(){
    //     return response()->json(User::all());
    // }
    // public function store(Request $request){
    //     $request->validate([
    //         'nom'          => 'required|string|max:255',
    //         'prenom'     => 'required|string|max:255',
    //         'email'         => 'required|email|unique:users,email',
    //         'password'      => 'required|string|min:8',
    //     ]);
    //     $file=$request->cv;

    
    //     $user=User::create([
    //         'nom'          => $request->nom,
    //         'prenom'     => $request->prenom,
    //         'date_naissance'           => $request->date_naissance,
    //         'phone'         => $request->phone,
    //        'lieudenaissance' => $request->lieudenaissance,
    //         'cv'            => $file->store('uploads', 'public'),
    //     //  'cover_letter'  => $request->cover_letter,
    //         'datedepot'     => $request->datedepot,
    //         // 'datevalidation'=> $request->datevalidation,
    //         'dateentretien' => $request->dateentretien,
    //         'cin'           => $request->cin,
    //         'poste'         => $request->poste,
    //         'nb_offres'     => $request->nb_offres,
    //         // 'nbvalidatio
    //         // n'  => $request->nbvalidation,
    //         'email'         => $request->email,
    //         'password'      =>Hash::make($request->password),
    //         'nomsociete'=> $request->nomsociete,
    //         'localisation'=> $request->localisation,



    
    //     ]);
    //     return response()->json($user, 201);
    // // }

// public function update(User $user,Request $request ){
//     $request->validate([
//         'nom'          => 'nullable|string|max:255',
//         'prenom'     => ' nullable|string|max:255',
//        // 'date_naissance'           => 'date',
//         'lieudenaissance'=> 'nullable|string',
//         'siteweb'=>'string',
//         'phone'         => 'nullable|string|max:20', 
//         'cover_letter'  => 'nullable|string',
//         'datedepot'     => 'nullable|date',
//         'datevalidation'=> 'nullable|date',
//        // 'dateentretien' => 'date',
//         'cin'           => 'nullable|string|max:20',
//         'email'         => 'nullable|string|max:20',
//         'poste'         => 'nullable|string|max:255',
//         //'nb_offres'     => 'integer|min:0',
//        // 'nbvalidation'  => 'integer|min:0',
//         'password'      => 'nullable|string|min:6',
//         'nomsociete'=>'nullable|string',
//         'localisation'=>'nullable|string',  // Correction ici



        
//     ]);
//     $user->update([
//         'nom'          => $request->nom,
//         'prenom'     => $request->prenom,
//         'date_naissance'           => $request->date_naissance,
//         'phone'         => $request->phone,
//         'lieudenaissance' => $request->lieudenaissance,
//         'cv'            => $request->cv, 
//         'cover_letter'  => $request->cover_letter,
//         'datedepot'     => $request->datedepot,
//         'datevalidation'=> $request->datevalidation,
//         'dateentretien' => $request->dateentretien,
//         'cin'           => $request->cin,
//         'poste'         => $request->poste,
//         'nb_offres'     => $request->nb_offres,
//         'nbvalidation'  => $request->nbvalidation,
//         'email'         => $request->email,
//         'password'      =>Hash::make($request->password),
//         'siteweb'=>$request->siteweb,
//         'nomsociete'=>$request->nomsociete,
//         'localisation'=>$request->localisation,





//     ]);
//     return response()->json(data: $user);
// }

public function updateMyprofile(Request $request) {
    $user = auth()->user();

    $request->validate([
        'nom' => 'nullable|string|max:255',
        'prenom' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6',
        'lieudenaissance' => 'nullable|string',
        'siteweb' => 'nullable|string',
        'phone' => 'nullable|string|max:20',
        'cover_letter' => 'nullable|string',
        'datedepot' => 'nullable|date',
        'datevalidation' => 'nullable|date',
        'dateentretien' => 'nullable|date',
        'cin' => 'nullable|string|max:20',
        'poste' => 'nullable|string|max:255',
        'nb_offres' => 'nullable|integer|min:0',
        'nbvalidation' => 'nullable|integer|min:0',
        'nomsociete' => 'nullable|string',
        'localisation' => 'nullable|string',
        'cv' => 'nullable|string',
    ]);

    $data = $request->all();

    $user->update($data);

    auth()->setUser($user->fresh());

    return response()->json(['message' => 'Profil mis à jour avec succès', 'user' => $user]);
}
  }
