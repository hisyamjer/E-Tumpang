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
        'destination',
        'departure_time',
        'date',
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
];

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

    

}
