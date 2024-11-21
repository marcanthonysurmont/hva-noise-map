<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('noise_levels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('hourly_average');
            $table->foreignId('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noise_levels');
    }
};
