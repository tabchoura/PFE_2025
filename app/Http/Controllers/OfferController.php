<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        return response()->json(Offre::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type'      => 'required|string|max:255',
            'description'    => 'required|string|max:255',
            'salary' => 'required|numeric',
            'user_id'   => 'nullable|integer|exists:users,id',
        ]);

        $offre = Offre::create([
            'type'          => $validatedData['type'],
            'description'        => $validatedData['description'],
            'salary'=> $validatedData['salary'] ,
            'user_id'       => $validatedData['user_id'] ,
        ]);

        return response()->json($offre, status: 201);
    }

    public function update(Request $request, $id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json(['message' => 'Offre de travail non trouvé'], 404);
        }

        $validatedData = $request->validate([
            'type'        => 'required|string',
            'description' => 'required|string',
            'salary'      => 'required|numeric',
            'user_id'     => 'required|exists:users,id',
        ]);
        
        $offre->update($validatedData);

        return response()->json($offre);
    }

    public function destroy($id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvé'], 404);
        }

        $offre->delete();

        return response()->json(['message' => 'Offre supprimé avec succès']);
    }
}
