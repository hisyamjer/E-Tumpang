<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'paymentID',
        'bookingID',
        'amount',
        'payment_method',
        'status',
        'payment_date',
    ];
}
