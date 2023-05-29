<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GreenhouseAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'air_temperature',
        'relative_air_humidity',
        'soil_temperature',
        'relative_humidity_of_the_soil',
        'lighting_intensity',
        'outside_air_temperature',
        'wind_speed',
        'water_level',
        'opening',
        'closing',
        'opened',
        'closed',
        'filling',
        'emptying',
        'full',
        'empty',
        'remote_mode',
    ];


    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }
}
