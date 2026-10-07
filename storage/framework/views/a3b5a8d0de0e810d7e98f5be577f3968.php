

<?php $__env->startSection('content'); ?>
<?php
    $isEmployee = ($user->account_type ?? $user->role) === 'employee';
    $personal = $user->personal_information ?? [];
    $family = $user->family_background ?? [];
    $student = $user->student_information ?? [];
    $employeeEducation = $user->employee_education ?? [];
    $civilService = $user->civil_service_eligibility ?? [];
    $workExperience = $user->work_experience ?? [];
    $voluntaryWork = $user->voluntary_work ?? [];
    $gadTraining = $user->gad_training ?? [];
    $otherInformation = $user->other_information ?? [];
    $employeeEducationJson = data_get($employeeEducation, 'text', is_array($employeeEducation) ? json_encode($employeeEducation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $employeeEducation);
    $civilServiceJson = data_get($civilService, 'text', is_array($civilService) ? json_encode($civilService, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $civilService);
    $workExperienceJson = data_get($workExperience, 'text', is_array($workExperience) ? json_encode($workExperience, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $workExperience);
    $voluntaryWorkJson = data_get($voluntaryWork, 'text', is_array($voluntaryWork) ? json_encode($voluntaryWork, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $voluntaryWork);
    $gadTrainingJson = data_get($gadTraining, 'text', is_array($gadTraining) ? json_encode($gadTraining, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $gadTraining);
    $personalDisabilities = implode(', ', $personal['disabilities'] ?? []);
    $studentAchievements = implode(', ', $student['achievements'] ?? []);
    $studentFinancingSources = implode(', ', $student['financing_sources'] ?? []);
?>

<?php echo $__env->make('profile._edit-form', ['user' => $user, 'personal' => $personal, 'family' => $family, 'student' => $student, 'otherInformation' => $otherInformation, 'employeeEducationJson' => $employeeEducationJson, 'civilServiceJson' => $civilServiceJson, 'workExperienceJson' => $workExperienceJson, 'voluntaryWorkJson' => $voluntaryWorkJson, 'gadTrainingJson' => $gadTrainingJson, 'personalDisabilities' => $personalDisabilities, 'studentAchievements' => $studentAchievements, 'studentFinancingSources' => $studentFinancingSources], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views/profile/edit-personal-data.blade.php ENDPATH**/ ?>