<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'trip';

    protected $primaryKey = 'tripID';

    public $timestamps = false;

    protected $fillable = [
        'tripID',
        'bookingID',
        'studentID',
        'car_model',
        'plate_number',
        'description',
        'gender_pref',
        'destination',
        'departure_at',
        'available_seats',
        'price',
        'status',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
    'latitude' => 'float',
    'longitude' => 'float',
    'departure_at' => 'datetime',
 ];

    protected $appends = ['seats_remaining'];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingID', 'bookingID');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentID', 'studentID');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tripID', 'tripID');
    }

    public function getSeatsRemainingAttribute()
    {
        return $this->available_seats - $this->bookings()->count();
    }

    public function feedback(){
        
        return $this->hasOne(Feedback::class, 'tripID', 'tripID');
    }

    

}
