<?php
namespace App\Http\Controllers;

use App\Models\Entretien;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    public function index()
    {
        // on renvoie aussi la candidature liée (join eager)
        return response()->json(
            Entretien::with('candidature')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidature_id' => 'required|exists:candidatures,id',
            'date_entretien' => 'required|date',
        ]);

        $entretien = Entretien::create($validated + [
            'statut' => 'planifié'
        ]);

        return response()->json($entretien->load('candidature'), 201);
    }

    public function update(Request $request, Entretien $entretien)
    {
        $validated = $request->validate([
            'date_entretien' => 'sometimes|date',
            'statut'         => 'sometimes|string|max:50',
        ]);

        $entretien->update($validated);

        return response()->json($entretien);
    }

    public function destroy(Entretien $entretien)
    {
        $entretien->delete();
        return response()->json(['message' => 'Entretien supprimé']);
    }
}
