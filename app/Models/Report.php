<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'incident_id',
        'cloak_alias',
        'nature',
        'course',
        'gender',
        'incident_date',
        'incident_time',
        'location',
        'description',
        'priority',
        'status',
        'seen_at',
        'evidence', // Idinagdag para sa file uploads
    ];

    /**
     * Casting: Siguraduhin na ang data types ay tama paglabas ng DB.
     */
    protected $casts = [
        'incident_date' => 'date',
        'seen_at' => 'datetime',
        'evidence' => 'array', // Ginagawang array ang JSON string mula sa DB
    ];

    /**
     * Normalized list of evidence paths.
     */
    public function getEvidenceFilesAttribute(): array
    {
        $evidence = $this->evidence;

        if (empty($evidence)) {
            return [];
        }

        if (is_string($evidence)) {
            $decoded = json_decode($evidence, true);
            $evidence = is_array($decoded) ? $decoded : [$evidence];
        }

        if (!is_array($evidence)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) {
            return is_string($path) && trim($path) !== '' ? trim($path) : null;
        }, $evidence)));
    }

    /**
     * Relasyon sa User (Ang nag-report)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: Check kung kailangan ng agarang aksyon
     */
    public function isEmergency()
    {
        return $this->status === 'Emergency' || $this->priority === 'High';
    }

    /**
     * UI Helper: Kunin ang tamang Badge Color base sa Status
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Emergency' => 'danger',
            'Pending' => 'warning',
            'Under Review' => 'info',
            'Resolved' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Scope: Kuhanin lamang ang mga aktibong emergency para sa Dashboard
     */
    public function scopeActiveEmergency($query)
    {
        return $query->where('status', 'Emergency')
                     ->orWhere('priority', 'High');
    }
}