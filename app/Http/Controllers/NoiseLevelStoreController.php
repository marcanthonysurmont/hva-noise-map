<?php

namespace App\Http\Controllers;

use App\Services\NoiseLevelBufferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoiseLevelStoreController extends Controller
{
    private NoiseLevelBufferService $bufferService;

    public function __construct(NoiseLevelBufferService $bufferService)
    {
        $this->bufferService = $bufferService;
    }

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'decibel_value' => 'required|numeric'
        ]);

        Log::info("Received reading", [
            'value' => $validated['decibel_value'],
            'time' => now()->format('Y-m-d H:i:s')
        ]);

        $this->bufferService->addReading($validated['decibel_value']);
        return response()->json(['message' => 'Reading buffered']);
    }
}
