<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStatusUpdate extends Model
{
    protected $fillable = [
        'report_id',
        'admin_id',
        'from_status',
        'to_status',
        'details',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
