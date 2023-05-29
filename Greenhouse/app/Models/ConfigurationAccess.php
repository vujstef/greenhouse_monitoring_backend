<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'min_air_temp',
        'min_wind_speed',
        'max_soil_temp',
        'max_soil_humidity'
    ];

    protected $table = 'configuration_command_access';

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }
}
