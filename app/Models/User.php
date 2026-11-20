<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $role
 * @property string|null $account_type
 * @property string|null $account_status
 * @property string|null $department
 * @property string|null $gender
 * @property string|null $employee_category
 * @property string|null $cloak_alias
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $phone_number
 * @property int|null $age
 * @property array|null $personal_information
 * @property array|null $family_background
 * @property array|null $student_information
 * @property array|null $employee_education
 * @property array|null $civil_service_eligibility
 * @property array|null $work_experience
 * @property array|null $voluntary_work
 * @property array|null $gad_training
 * @property array|null $other_information
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'failed_login_attempts',
        'password_locked_at',
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
        'password_locked_at' => 'datetime',
    ];

    public function getNameAttribute($value): string
    {
        $nameParts = array_filter([
            trim((string) ($this->attributes['first_name'] ?? '')),
            trim((string) ($this->attributes['middle_name'] ?? '')),
            trim((string) ($this->attributes['last_name'] ?? '')),
        ], fn ($part) => $part !== '');

        return $nameParts ? implode(' ', $nameParts) : (string) ($value ?? '');
    }

    public function hasCompletedPersonalData(): bool
    {
        $isEmployee = strtolower((string) ($this->account_type ?? $this->role)) === 'employee';
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

        if (! $isEmployee) {
            $student = $this->student_information ?? [];
            foreach (['year_level', 'school_type', 'last_school_attended', 'financing_sources'] as $field) {
                if (blank($student[$field] ?? null)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function customerSatisfactionSurvey()
    {
        return $this->hasOne(CustomerSatisfactionSurvey::class);
    }

    public function isGadCoordinator(): bool
    {
        return strtolower(trim((string) $this->role)) === 'admin'
            && (
                strtolower(trim((string) $this->account_type)) === 'coordinator'
                || strtolower(trim((string) $this->email)) === 'gadcoordinator@psu.edu.ph'
            );
    }
}
