<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'student_id',
        'purpose_of_visit',
        'urgency_level',
        'scheduled_date',
        'scheduled_time',
        'description',
        'format',
        'status',
        'admin_notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}