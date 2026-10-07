<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'age',
        'gender',
        'department',
        'phone_number',
        'password',
        'role',
        'account_type',
        'employee_category',
        'cloak_alias',
        'id_image_path',
        'selfie_image_path',
        'profile_photo_path',
        'account_status',
        'availability_status',
        'last_seen_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'last_name',
        'first_name',
        'middle_name',
        'personal_information',
        'family_background',
        'student_information',
        'employee_education',
        'civil_service_eligibility',
        'work_experience',
        'voluntary_work',
        'gad_training',
        'other_information',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'approved_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'personal_information' => 'array',
        'family_background' => 'array',
        'student_information' => 'array',
        'employee_education' => 'array',
        'civil_service_eligibility' => 'array',
        'work_experience' => 'array',
        'voluntary_work' => 'array',
        'gad_training' => 'array',
        'other_information' => 'array',
    ];

    public function hasCompletedPersonalData(): bool
    {
        $isEmployee = ($this->account_type ?? $this->role) === 'employee';
        $required = [
            $this->name,
            $this->email,
            $this->last_name,
            $this->first_name,
            $this->phone_number,
            $this->age,
            $this->gender,
            $this->department,
        ];

        foreach ($required as $value) {
            if (blank($value)) {
                return false;
            }
        }

        $personal = $this->personal_information ?? [];
        foreach ([
            'date_of_birth',
            'place_of_birth',
            'sex',
            'gender_identity',
            'citizenship',
            'blood_type',
            'height',
            'weight',
            'landline_number',
            'civil_status',
            'current_address',
            'home_address',
            'religion',
        ] as $field) {
            if (blank($personal[$field] ?? null)) {
                return false;
            }
        }

        if (!$isEmployee) {
            $student = $this->student_information ?? [];
            foreach (['year_level', 'school_type', 'last_school_attended', 'financing_sources'] as $field) {
                if (blank($student[$field] ?? null)) {
                    return false;
                }
            }
        }

        return true;
    }
}