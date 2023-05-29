<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class measuring_status extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function greenhouse(){
        return $this->belongsToMany(Greenhouse::class, 'greenhouse_measuring_status', 'measuring_status_id', 'greenhouse_id');
    }

    protected $hidden = ['pivot', 'updated_at'];
}
