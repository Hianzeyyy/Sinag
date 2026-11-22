@extends('layouts.app')

@section('content')
@php
    $isEmployee = strtolower(trim((string) ($user->account_type ?? $user->role))) === 'employee';
    $personal = $user->personal_information ?? [];
    $family = $user->family_background ?? [];
    $student = $user->student_information ?? [];
    $employeeEducation = $user->employee_education ?? [];
    $civilService = $user->civil_service_eligibility ?? [];
    $workExperience = $user->work_experience ?? [];
    $voluntaryWork = $user->voluntary_work ?? [];
    $gadTraining = $user->gad_training ?? [];
    $otherInformation = $user->other_information ?? [];
    $personalDisabilities = implode(', ', $personal['disabilities'] ?? []);
    $studentAchievements = implode(', ', $student['achievements'] ?? []);
    $studentFinancingSources = implode(', ', $student['financing_sources'] ?? []);
@endphp

@include('profile._edit-form', ['user' => $user, 'personal' => $personal, 'family' => $family, 'student' => $student, 'otherInformation' => $otherInformation, 'employeeEducation' => $employeeEducation, 'civilService' => $civilService, 'workExperience' => $workExperience, 'voluntaryWork' => $voluntaryWork, 'gadTraining' => $gadTraining, 'personalDisabilities' => $personalDisabilities, 'studentAchievements' => $studentAchievements, 'studentFinancingSources' => $studentFinancingSources])

@endsection
