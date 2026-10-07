<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'email',
        'name',
        'student_employee_id',
        'department',
        'id_picture_path',
        'reason',
        'status',
        'admin_id',
        'admin_remarks',
        'reset_token_hash',
        'reset_token_expires_at',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'reset_token_expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $hidden = ['reset_token_hash'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}