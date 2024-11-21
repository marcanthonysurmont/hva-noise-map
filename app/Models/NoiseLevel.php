<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoiseLevel extends Model
{
    protected $fillable = ['hourly_average', 'location_id', 'created_at'];
}
