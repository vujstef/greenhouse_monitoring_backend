<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;
    protected $fillable = [
        'min_air_temp',
        'min_wind_speed',
        'max_soil_temp',
        'max_soil_humidity',
    ];

    protected $table = 'configuration_command';

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }
}
