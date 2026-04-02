<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';
    protected $primaryKey = 'bookingID';

    public $timestamps = false;

    protected $fillable = [
        'bookingID',
        'studentID',
        'tripID',
        'seats_booked',
        'status',
        'booking_date',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'tripID', 'tripID');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentID', 'studentID');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'bookingID', 'bookingID');
    }
}
