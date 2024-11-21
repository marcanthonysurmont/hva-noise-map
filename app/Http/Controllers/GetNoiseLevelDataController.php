<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NoiseLevel;
use Carbon\Carbon;

class GetNoiseLevelDataController extends Controller
{
    private const DEFAULT_LOCATION_ID = 1;

    public function __invoke(Request $request)
    {
        $date = Carbon::parse($request->date);

        // Get all noise levels for the date
        $noiseLevels = NoiseLevel::where('location_id', self::DEFAULT_LOCATION_ID)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        // Initialize arrays with all 24 hours
        $data = array_fill(0, 24, null);
        $labels = array_map(function($hour) {
            return sprintf("%02d:00", $hour);
        }, range(0, 23));

        // Group and average by hour
        foreach ($noiseLevels as $level) {
            $hour = (int)$level->created_at->format('H');
            if (!isset($data[$hour])) {
                $data[$hour] = [];
            }
            $data[$hour][] = $level->hourly_average;
        }

        // Calculate averages for hours with data
        $data = array_map(function($hourData) {
            return $hourData ? round(array_sum($hourData) / count($hourData)) : null;
        }, $data);

        // Remove null values and corresponding labels
        $validData = [];
        $validLabels = [];
        foreach ($data as $hour => $value) {
            if ($value !== null) {
                $validData[] = $value;
                $validLabels[] = $labels[$hour];
            }
        }

        return response()->json([
            'labels' => $validLabels,
            'data' => $validData
        ]);
    }
}
