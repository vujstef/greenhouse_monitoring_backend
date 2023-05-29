<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_command',
        'closing_command',
        'filling_command',
        'emptying_command',
        'remote_mode',
    ];

    protected $table = 'managements_command';

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }
}
