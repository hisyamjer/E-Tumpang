<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'student';
    protected $primaryKey = 'studentID';
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
    ];
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];
    public $incrementing = false;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'studentID', 'studentID');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'studentID', 'studentID');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'studentID', 'studentID');
    }
}


