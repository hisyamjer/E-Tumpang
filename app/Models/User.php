<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'userID';

    protected $fillable = [
        'userID',
        'studentID',
        'role',
        'plate_number',
        'created_at',
        'updated_at'
    ];
}
