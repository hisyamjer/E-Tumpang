<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'adminID';

    protected $fillable = [
        'adminID',
        'email',
        'password',
        'created_at',
    ];

    protected $hidden = [
        'password',
    ];


}
