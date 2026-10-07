<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GadSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'available_from',
        'available_until',
        'details',
        'status',
        'created_by',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}