<?php

namespace App\Http\Controllers;

use App\Models\NoiseLevel;
use Illuminate\Http\Request;

class NoiseLevelStoreController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'decibel_value' => 'required|numeric'
        ]);

        $noiseLevel = new NoiseLevel();
        $noiseLevel->hourly_average = $validated['decibel_value'];
        $noiseLevel->location_id = 1;
        $noiseLevel->save();

        return response()->json(['message' => 'Reading stored']);
    }
}
