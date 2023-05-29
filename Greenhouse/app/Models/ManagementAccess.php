<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'opening_command',
        'closing_command',
        'filling_command',
        'emptying_command',
        'remote_mode'
    ];

    protected $table = 'management_command_access';

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }
}
