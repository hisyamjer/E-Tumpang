<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'feedbackID',
        'studentID',
        'tripID',
        'rating',
        'comment',
        'feedback_date',
    ];
}
