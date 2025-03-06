<?php

namespace App\Http\Controllers;

use App\Models\Entretien;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    public function index()
    {
        return response()->json(Entretien::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'statut'    => 'required|string|max:255',
            'date_entretien' => 'nullable|date',
            'time'      => 'nullable|date_format:H:i',
            'user_id'   => 'nullable|integer|exists:users,id',
        ]);

        $entretien = Entretien::create([
            'name'          => $validatedData['name'],
            'statut'        => $validatedData['statut'],
            'date_entretien'=> $validatedData['date_entretien'] ?? null,
            'time'          => $validatedData['time'] ?? null,
            'user_id'       => $validatedData['user_id'] ?? null,
        ]);

        return response()->json($entretien, 201);
    }

    public function update(Request $request, $id)
    {
        $entretien = Entretien::find($id);

        if (!$entretien) {
            return response()->json(['message' => 'Entretien non trouvé'], 404);
        }

        $validatedData = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'statut'    => 'sometimes|string|max:255',
            'date_entretien' => 'sometimes|date',
            'time'      => 'sometimes|date_format:H:i',
            'user_id'   => 'sometimes|integer|exists:users,id',
        ]);

        $entretien->update($validatedData);

        return response()->json($entretien);
    }

    public function destroy($id)
    {
        $entretien = Entretien::find($id);

        if (!$entretien) {
            return response()->json(['message' => 'Entretien non trouvé'], 404);
        }

        $entretien->delete();

        return response()->json(['message' => 'Entretien supprimé avec succès']);
    }
}
