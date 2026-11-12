<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSatisfactionSurvey extends Model
{
    protected $fillable = [
        'user_id',
        'rating',
        'feedback',
        'responses',
    ];

    protected $casts = [
        'rating' => 'integer',
        'responses' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
