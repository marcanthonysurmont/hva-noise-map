<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\NoiseLevelBufferService;
use App\Models\NoiseLevel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NoiseLevelBufferTest extends TestCase
{
    // use DatabaseTransactions;

    public function test_simulated_hour_boundaries()
    {
        NoiseLevel::query()->delete();
        DB::enableQueryLog();

        // Start with current time
        $startTime = Carbon::now();
        $buffer = new NoiseLevelBufferService();

        // Calculate minutes until next hour dynamically
        $minutesUntilNextHour = 60 - $startTime->minute;

        Log::info("Test starting", [
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'minutes_to_next' => $minutesUntilNextHour
        ]);

        // Add readings until next hour
        for ($i = 0; $i <= $minutesUntilNextHour; $i++) {
            $currentTime = $startTime->copy()->addMinutes($i);
            $buffer->setMockTime($currentTime);
            $buffer->addReading(76.0);

            Log::debug("Added reading", [
                'time' => $currentTime->format('H:i:s'),
                'buffer_size' => count($buffer->getBufferContents())
            ]);
        }

        // Force save at next hour boundary
        $nextHour = $startTime->copy()->addMinutes($minutesUntilNextHour)->startOfHour();
        $buffer->setMockTime($nextHour);
        $buffer->forceSave();

        // Verify save at next hour boundary
        $record = NoiseLevel::where('created_at', $nextHour->format('Y-m-d H:00:00'))->first();

        $this->assertNotNull($record, "No record saved at hour boundary");
        $this->assertEquals(76.0, $record->hourly_average);
    }

    public function test_multiple_hour_boundaries()
    {
        NoiseLevel::query()->delete();
        DB::enableQueryLog();

        $buffer = new NoiseLevelBufferService();

        // Test scenarios
        $times = [
            // Current hour (17:25 -> 18:00)
            ['start' => Carbon::now(), 'end' => Carbon::now()->endOfHour()],
            // Next full hour (18:00 -> 19:00)
            ['start' => Carbon::now()->startOfHour()->addHour(), 'end' => Carbon::now()->startOfHour()->addHours(2)]
        ];

        foreach ($times as $period) {
            $startTime = $period['start'];
            $endTime = $period['end'];
            $minutesInPeriod = $startTime->diffInMinutes($endTime);

            $buffer->setStartTime($startTime);
            $buffer->setMockTime($startTime);

            // Add readings for each minute
            for ($i = 0; $i <= $minutesInPeriod; $i++) {
                $currentTime = $startTime->copy()->addMinutes($i);
                $buffer->setMockTime($currentTime);
                $buffer->addReading(76.0);
            }
        }

        // Verify saves occurred at hour boundaries
        $records = NoiseLevel::orderBy('created_at')->get();
        $this->assertEquals(2, $records->count(), "Should save at both hour boundaries");
    }
}
