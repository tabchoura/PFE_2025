<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    public function index()
    {
        return response()->json(Entreprise::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'email'    => 'required|string|max:255',
            'department'    => 'required|string|max:255',
            'phone'    => 'required|string|max:255',



            
        ]);

        $entreprise = Entreprise::create([
            'name'          => $validatedData['name'],
            'location'        => $validatedData['location'],
            'email'        => $validatedData['email'],
            'department'        => $validatedData['department'],
            'phone'        => $validatedData['phone'],

        ]);

        return response()->json($entreprise, status: 201);
    }

    public function update(Request $request, $id)
    {
        $entreprise = Entreprise::find($id);

        if (!$entreprise) {
            return response()->json(['message' => 'Entreprise non trouvé'], 404);
        }

        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'email'    => 'required|string|max:255',
            'department'    => 'required|string|max:255',
            'phone'    => 'required|string|max:255',
            'description'=>'required|string|max:255',
            'siteweb'=>'required|string|max:255',
        ]);
        
        $entreprise->update($validatedData);

        return response()->json($entreprise);
    }

    public function destroy($id)
    {
        $entreprise = Entreprise::find($id);

        if (!$entreprise) {
            return response()->json(['message' => 'Entreprise non trouvé'], 404);
        }

        $entreprise->delete();

        return response()->json(['message' => 'Entreprise supprimé avec succès']);
    }
}
