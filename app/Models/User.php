<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'userID',
        'studentID',
        'role',
        'plate_number',
        'created_at',
        'updated_at'
    ];
}
