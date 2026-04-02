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

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'tripID', 'tripID');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentID', 'studentID');
    }
}
