<?php
// app/Services/NoiseLevelBufferService.php

namespace App\Services;

use App\Models\NoiseLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NoiseLevelBufferService
{
    private array $buffer = [];
    private ?Carbon $startTime = null;
    private int $minutesUntilNextHour;
    private ?Carbon $mockTime = null;

    public function __construct()
    {
        Log::info("Service constructed");
        $this->startTime = $this->getCurrentTime();
        $this->minutesUntilNextHour = 60 - $this->startTime->minute;
    }

    // For testing
    public function setMockTime(?Carbon $time): void
    {
        $this->mockTime = $time;
    }

    private function getCurrentTime(): Carbon
    {
        return $this->mockTime ?? Carbon::now('Europe/Amsterdam');
    }

    public function addReading(float $value): void
    {
        $now = $this->getCurrentTime();
        $elapsed = $now->diffInSeconds($this->startTime);

        Log::info("Processing reading", [
            'value' => $value,
            'current_time' => $now->format('Y-m-d H:i:s'),
            'start_time' => $this->startTime->format('Y-m-d H:i:s'),
            'elapsed' => $elapsed,
            'target' => $this->minutesUntilNextHour * 60,
            'hour_changed' => ($now->hour !== $this->startTime->hour)
        ]);

        // Add to buffer
        $this->buffer[] = $value;

        Log::debug("Buffer state", [
            'time' => $now->format('H:i:s'),
            'elapsed' => $elapsed,
            'target' => $this->minutesUntilNextHour * 60,
            'buffer_size' => count($this->buffer)
        ]);

        // Check hour boundary by both elapsed time AND hour change
        if ($elapsed >= $this->minutesUntilNextHour * 60 || $now->hour !== $this->startTime->hour) {
            Log::info("Boundary reached - saving", [
                'readings_count' => count($this->buffer),
                'elapsed_minutes' => floor($elapsed / 60)
            ]);
            $this->forceSave(); // Use forceSave method

            // Reset after save
            $this->startTime = $now;
            $this->minutesUntilNextHour = 60 - $now->minute;
        }
    }

    public function getBufferContents(): array
    {
        return $this->buffer;
    }

    public function setStartTime(Carbon $time): void
    {
        Log::info("Setting start time", [
            'time' => $time->format('Y-m-d H:i:s')
        ]);

        $this->startTime = $time;
        $this->minutesUntilNextHour = 60 - $time->minute;
    }

    public function forceSave(): void
    {
        if (!empty($this->buffer)) {
            $average = round(array_sum($this->buffer) / count($this->buffer), 2);

            NoiseLevel::create([
                'hourly_average' => $average,
                'location_id' => 1,
                'created_at' => $this->getCurrentTime()->startOfHour()
            ]);

            $this->buffer = [];
        }
    }
}
