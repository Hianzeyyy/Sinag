<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportConsent extends Model
{
    public const VERSION = '2026-12-01-v1';

    protected $fillable = [
        'user_id',
        'version',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];
}
