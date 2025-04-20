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
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);  // Si l'utilisateur n'est pas trouvé, renvoie une erreur
        }
    
        return response()->json($user);  // Retourne les données utilisateur en JSON
    }
    
    public function index(){
        return response()->json(User::all());
    }
    public function store(Request $request){
        $request->validate([
            'nom'          => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            //'date_naissance'           => 'required|date',
//            'lieudenaissance'=> 'required|string',
       
  //          'phone'         => 'required|string|max:20', 
    //        'cover_letter'  => 'string',
      //      'datedepot'     => 'nullable|date',
        //    'datevalidation'=> '|date',
          //  'dateentretien' => 'date',
            //'cin'           => 'required|string|max:20',
            //'poste'         => 'string|max:255',
            //'nb_offres'     => 'integer|min:0',
            //'nbvalidation'  => 'integer|min:0',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
        ]);
        $file=$request->cv;
    
        $user=User::create([
            'nom'          => $request->nom,
            'prenom'     => $request->prenom,
            'date_naissance'           => $request->date_naissance,
            'phone'         => $request->phone,
            'lieudenaissance' => $request->lieudenaissance,
            'cv'            => $file->store('uploads', 'public'),
            'cover_letter'  => $request->cover_letter,
            'datedepot'     => $request->datedepot,
            'datevalidation'=> $request->datevalidation,
            'dateentretien' => $request->dateentretien,
            'cin'           => $request->cin,
            'poste'         => $request->poste,
            'nb_offres'     => $request->nb_offres,
            'nbvalidation'  => $request->nbvalidation,
            'email'         => $request->email,
            'password'      =>Hash::make($request->password),
            'nomsociete'=>$request->nomsociete,
    
        ]);
        return response()->json($user, 201);
    }

public function update(User $user,Request $request ){
    $request->validate([
        'nom'          => 'required|string|max:255',
        'prenom'     => 'required|string|max:255',
        'date_naissance'           => 'required|date',
        'lieudenaissance'=> 'required|string',
        'siteweb'=>'string',
        'phone'         => 'required|string|max:20', 
        'cover_letter'  => 'string',
        'datedepot'     => 'nullable|date',
        'datevalidation'=> '|date',
        'dateentretien' => 'date',
        'cin'           => 'required|string|max:20',
        'poste'         => 'string|max:255',
        'nb_offres'     => 'integer|min:0',
        'nbvalidation'  => 'integer|min:0',
'email' => 'required|email|unique:users,email,' . $user->id,
        'password'      => 'required|string|min:6',
        'nomsociete'=>'string',
    ]);
    $user->update([
        'nom'          => $request->nom,
        'prenom'     => $request->prenom,
        'date_naissance'           => $request->date_naissance,
        'phone'         => $request->phone,
        'lieudenaissance' => $request->lieudenaissance,
        'cv'            => $request->cv, 
        'cover_letter'  => $request->cover_letter,
        'datedepot'     => $request->datedepot,
        'datevalidation'=> $request->datevalidation,
        'dateentretien' => $request->dateentretien,
        'cin'           => $request->cin,
        'poste'         => $request->poste,
        'nb_offres'     => $request->nb_offres,
        'nbvalidation'  => $request->nbvalidation,
        'email'         => $request->email,
        'password'      =>Hash::make($request->password),
        'siteweb'=>$request->siteweb,
        'nomsociete'=>$request->nomsociete,



    ]);
    return response()->json($user, 201);
}
}