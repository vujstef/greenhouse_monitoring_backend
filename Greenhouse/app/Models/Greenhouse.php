<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Greenhouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function status()
    {
        return $this->belongsToMany(measuring_status::class, 'greenhouse_measuring_status', 'greenhouse_id', 'measuring_status_id')
            ->withPivot('created_at')
            ->withTimestamps();
    }

    public function greenhouse()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function greenhouse_accesses()
    {
        return $this->hasMany(GreenhouseAccess::class);
    }

    public function thingspeak()
    {
        return $this->hasOne(Thingspeak::class);
    }

    public function configuration_access()
    {
        return $this->hasMany(ConfigurationAccess::class);
    }

    public function management_access()
    {
        return $this->hasMany(ManagementAccess::class);
    }

    public function configuration()
    {
        return $this->hasOne(Configuration::class);
    }

    public function management()
    {
        return $this->hasOne(Management::class);
    }
}
