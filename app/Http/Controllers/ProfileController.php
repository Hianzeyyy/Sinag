<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CloudinaryMedia;
use App\Support\MediaResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $verificationImagePath = $request->user()->id_image_path ?: $request->user()->selfie_image_path;
        $verificationImageAvailable = false;

        $verificationImageAvailable = MediaResolver::exists($verificationImagePath, [
            'local',
            config('filesystems.user_private_disk'),
            'public',
            config('filesystems.user_public_disk'),
        ]);

        return view('profile.show', [
            'user' => $request->user(),
            'verificationImageAvailable' => $verificationImageAvailable,
        ]);
    }

    public function personalData(Request $request)
    {
        return view('profile.personal-data', [
            'user' => $request->user(),
        ]);
    }

    public function editPersonalData(Request $request)
    {
        return view('profile.edit-personal-data', [
            'user' => $request->user(),
            'returnToSuggestions' => $request->boolean('return_to_suggestions'),
        ]);
    }

    public function adminPersonalData(Request $request, int $id)
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('profile.personal-data', [
            'user' => User::findOrFail($id),
            'adminViewer' => true,
        ]);
    }

    public function verificationImage(Request $request, User $user)
    {
        abort_unless(
            $request->user()->role === 'admin' || $request->user()->is($user),
            403
        );

        $path = $user->id_image_path ?: $user->selfie_image_path;
        abort_if(blank($path), 404);

        $response = MediaResolver::fileResponse($path, [
            'local',
            config('filesystems.user_private_disk'),
            'public',
            config('filesystems.user_public_disk'),
        ], [
            'Cache-Control' => 'private, no-store',
        ]);
        abort_if($response === null, 404);
        $response->setPrivate();

        return $response;
    }

    public function updateBasics(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'department' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'cloak_alias' => ['nullable', 'string', 'max:255', Rule::unique('users', 'cloak_alias')->ignore($user->id)],
        ]);

        $updates = $validated;
        $nameParts = preg_split('/\s+/', trim($validated['name']), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $updates['name'] = trim($validated['name']);

        if (Schema::hasColumn('users', 'first_name')
            && Schema::hasColumn('users', 'middle_name')
            && Schema::hasColumn('users', 'last_name')) {
            $updates['first_name'] = array_shift($nameParts) ?? '';
            $updates['last_name'] = $nameParts ? array_pop($nameParts) : null;
            $updates['middle_name'] = $nameParts ? implode(' ', $nameParts) : null;
        }

        $user->update($updates);

        return redirect()->route('profile.show')->with('status', 'Profile information updated.');
    }

    public function profilePhoto(User $user)
    {
        $path = $this->normalizeProfilePhotoPath($user->profile_photo_path);
        $response = MediaResolver::fileResponse($path, [
            'public',
            config('filesystems.user_public_disk'),
            'local',
            config('filesystems.user_private_disk'),
        ]);
        if ($response === null) {
            $response = response($this->defaultAvatarSvg($user->name, $user->role), 200, [
                'Content-Type' => 'image/svg+xml',
            ]);
        }

        $response->setPrivate();
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }

    private function normalizeProfilePhotoPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (CloudinaryMedia::hasReferenceScheme($path)) {
            return CloudinaryMedia::parseReference($path) !== null ? $path : null;
        }

        $path = ltrim(str_replace('\\', '/', trim($path)), '/');

        foreach (['storage/app/public/', 'app/public/', 'public/storage/', 'storage/', 'public/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
                break;
            }
        }

        $segments = explode('/', $path);
        if ($path === '' || in_array('', $segments, true) || in_array('.', $segments, true) || in_array('..', $segments, true)) {
            return null;
        }

        return $path;
    }

    private function defaultAvatarSvg(string $name, string $role): string
    {
        $name = trim($name);
        $initial = mb_strtoupper(mb_substr($name !== '' ? $name : 'User', 0, 1, 'UTF-8'), 'UTF-8');
        $initial = htmlspecialchars($initial, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $background = strtolower(trim($role)) === 'admin' ? '#7c3aed' : '#8f72f5';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="128" height="128">
    <rect width="128" height="128" rx="64" fill="{$background}" />
    <text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle"
          fill="#ffffff" font-family="Arial, sans-serif" font-size="58" font-weight="700">{$initial}</text>
</svg>
SVG;
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isEmployee = strtolower(trim((string) ($user->account_type ?? $user->role))) === 'employee';

        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'department' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'cloak_alias' => ['nullable', 'string', 'max:255', Rule::unique('users', 'cloak_alias')->ignore($user->id)],
            'personal_information' => ['required', 'array'],
            'personal_information.date_of_birth' => ['required', 'string', 'max:255'],
            'personal_information.place_of_birth' => ['required', 'string', 'max:255'],
            'personal_information.sex' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'personal_information.gender_identity' => ['required', 'string', 'max:100'],
            'personal_information.citizenship' => ['required', 'string', 'max:100'],
            'personal_information.blood_type' => ['required', 'string', 'max:20'],
            'personal_information.height' => ['required', 'numeric', 'min:0', 'max:500'],
            'personal_information.weight' => ['required', 'numeric', 'min:0', 'max:500'],
            'personal_information.landline_number' => ['required', 'string', 'max:50'],
            'personal_information.civil_status' => ['required', 'string', 'max:100'],
            'personal_information.current_address' => ['required', 'string', 'max:500'],
            'personal_information.home_address' => ['required', 'string', 'max:500'],
            'personal_information.religion' => ['required', 'string', 'max:100'],
            'personal_information.religion_other' => ['nullable', 'string', 'max:100'],
            'personal_information.disabilities_text' => ['nullable', 'string', 'max:1000'],
            'personal_information.disability_other' => ['nullable', 'string', 'max:255'],
            'family_background' => ['nullable', 'array'],
            'family_background.spouse_name' => ['nullable', 'string', 'max:255'],
            'family_background.spouse_occupation' => ['nullable', 'string', 'max:255'],
            'family_background.father_name' => ['nullable', 'string', 'max:255'],
            'family_background.father_occupation' => ['nullable', 'string', 'max:255'],
            'family_background.father_age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'family_background.mother_name' => ['nullable', 'string', 'max:255'],
            'family_background.mother_occupation' => ['nullable', 'string', 'max:255'],
            'family_background.mother_age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'family_background.children_total' => ['nullable', 'integer', 'min:0', 'max:100'],
            'family_background.children_boys' => ['nullable', 'integer', 'min:0', 'max:100'],
            'family_background.children_girls' => ['nullable', 'integer', 'min:0', 'max:100'],
            'family_background.brothers_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'family_background.sisters_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'family_background.monthly_income' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'family_background.family_type' => ['nullable', 'string', 'max:100'],
            'family_background.family_type_other' => ['nullable', 'string', 'max:255'],
            'family_background.home_ownership_type' => ['nullable', 'string', 'max:100'],
            'family_background.home_ownership_other' => ['nullable', 'string', 'max:255'],
            'family_background.residential_home_type' => ['nullable', 'string', 'max:100'],
            'family_background.residential_home_other' => ['nullable', 'string', 'max:255'],
            'student_information' => [$isEmployee ? 'nullable' : 'required', 'array'],
            'student_information.year_level' => [Rule::requiredIf(! $isEmployee), 'nullable', 'string', 'max:100'],
            'student_information.school_type' => [Rule::requiredIf(! $isEmployee), 'nullable', 'string', 'max:100'],
            'student_information.last_school_attended' => [Rule::requiredIf(! $isEmployee), 'nullable', 'string', 'max:255'],
            'student_information.achievement_other_rank' => ['nullable', 'string', 'max:255'],
            'student_information.achievements_text' => ['nullable', 'string', 'max:1000'],
            'student_information.financing_sources_text' => [Rule::requiredIf(! $isEmployee), 'nullable', 'string', 'max:1000'],
            'student_information.financing_other_work' => ['nullable', 'string', 'max:255'],
            'student_information.gad_training_attended' => ['nullable', 'in:0,1'],
            'student_information.gad_training_details' => ['nullable', 'string', 'max:1000'],
            'other_information' => ['nullable', 'array'],
            'other_information.emp_skills_hobbies' => ['nullable', 'string', 'max:1000'],
            'other_information.emp_distinctions' => ['nullable', 'string', 'max:1000'],
            'other_information.emp_membership' => ['nullable', 'string', 'max:1000'],
            'employee_education_json' => ['nullable', 'string', 'max:5000'],
            'civil_service_eligibility_json' => ['nullable', 'string', 'max:5000'],
            'work_experience_json' => ['nullable', 'string', 'max:5000'],
            'voluntary_work_json' => ['nullable', 'string', 'max:5000'],
            'gad_training_json' => ['nullable', 'string', 'max:5000'],
            'employee_education' => ['nullable', 'array', 'max:4'],
            'employee_education.*' => ['array:level,school,degree,from,to'],
            'employee_education.*.level' => ['required', Rule::in(['Elementary', 'High School', 'Vocational/Trade Course', 'College'])],
            'employee_education.*.school' => ['nullable', 'string', 'max:255'],
            'employee_education.*.degree' => ['nullable', 'string', 'max:255'],
            'employee_education.*.from' => ['nullable', 'string', 'max:100'],
            'employee_education.*.to' => ['nullable', 'string', 'max:100'],
            'civil_service_eligibility' => ['nullable', 'array', 'max:1'],
            'civil_service_eligibility.*' => ['array:career,rating,exam_date,exam_place,license_num,release_date'],
            'civil_service_eligibility.*.career' => ['nullable', 'string', 'max:255'],
            'civil_service_eligibility.*.rating' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'civil_service_eligibility.*.exam_date' => ['nullable', 'date'],
            'civil_service_eligibility.*.exam_place' => ['nullable', 'string', 'max:255'],
            'civil_service_eligibility.*.license_num' => ['nullable', 'string', 'max:255'],
            'civil_service_eligibility.*.release_date' => ['nullable', 'date'],
            'work_experience' => ['nullable', 'array', 'max:1'],
            'work_experience.*' => ['array:position,department,from,to,appointment_status,salary,grade_step,govt_service'],
            'work_experience.*.position' => ['nullable', 'string', 'max:255'],
            'work_experience.*.department' => ['nullable', 'string', 'max:255'],
            'work_experience.*.from' => ['nullable', 'date'],
            'work_experience.*.to' => ['nullable', 'date'],
            'work_experience.*.appointment_status' => ['nullable', 'string', 'max:100'],
            'work_experience.*.salary' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'work_experience.*.grade_step' => ['nullable', 'string', 'max:100'],
            'work_experience.*.govt_service' => ['nullable', Rule::in(['Yes', 'No'])],
            'voluntary_work' => ['nullable', 'array', 'max:1'],
            'voluntary_work.*' => ['array:org_name,org_address,hours,position,from,to'],
            'voluntary_work.*.org_name' => ['nullable', 'string', 'max:255'],
            'voluntary_work.*.org_address' => ['nullable', 'string', 'max:500'],
            'voluntary_work.*.hours' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'voluntary_work.*.position' => ['nullable', 'string', 'max:255'],
            'voluntary_work.*.from' => ['nullable', 'date'],
            'voluntary_work.*.to' => ['nullable', 'date'],
            'gad_training' => ['nullable', 'array', 'max:1'],
            'gad_training.*' => ['array:title,from,to,hours,conducted_by'],
            'gad_training.*.title' => ['nullable', 'string', 'max:255'],
            'gad_training.*.from' => ['nullable', 'date'],
            'gad_training.*.to' => ['nullable', 'date'],
            'gad_training.*.hours' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'gad_training.*.conducted_by' => ['nullable', 'string', 'max:255'],
        ]);

        $personalInformation = $validated['personal_information'] ?? [];
        $familyBackground = $validated['family_background'] ?? [];
        $studentInformation = $validated['student_information'] ?? [];
        $otherInformation = $validated['other_information'] ?? [];
        $name = implode(' ', array_filter([
            trim($validated['first_name']),
            trim($validated['middle_name'] ?? ''),
            trim($validated['last_name']),
        ], fn ($part) => $part !== ''));

        $user->update([
            'name' => $name,
            'last_name' => $validated['last_name'] ?? null,
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'department' => $validated['department'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'cloak_alias' => $validated['cloak_alias'] ?? null,
            'personal_information' => [
                'date_of_birth' => $personalInformation['date_of_birth'] ?? null,
                'place_of_birth' => $personalInformation['place_of_birth'] ?? null,
                'sex' => $personalInformation['sex'] ?? null,
                'gender_identity' => $personalInformation['gender_identity'] ?? null,
                'citizenship' => $personalInformation['citizenship'] ?? null,
                'blood_type' => $personalInformation['blood_type'] ?? null,
                'height' => $personalInformation['height'] ?? null,
                'weight' => $personalInformation['weight'] ?? null,
                'landline_number' => $personalInformation['landline_number'] ?? null,
                'civil_status' => $personalInformation['civil_status'] ?? null,
                'current_address' => $personalInformation['current_address'] ?? null,
                'home_address' => $personalInformation['home_address'] ?? null,
                'religion' => $personalInformation['religion'] ?? null,
                'religion_other' => $personalInformation['religion_other'] ?? null,
                'disabilities' => $this->parseCommaSeparatedList($personalInformation['disabilities_text'] ?? null),
                'disability_other' => $personalInformation['disability_other'] ?? null,
            ],
            'family_background' => [
                'spouse_name' => $familyBackground['spouse_name'] ?? null,
                'spouse_occupation' => $familyBackground['spouse_occupation'] ?? null,
                'father_name' => $familyBackground['father_name'] ?? null,
                'father_occupation' => $familyBackground['father_occupation'] ?? null,
                'father_age' => $familyBackground['father_age'] ?? null,
                'mother_name' => $familyBackground['mother_name'] ?? null,
                'mother_occupation' => $familyBackground['mother_occupation'] ?? null,
                'mother_age' => $familyBackground['mother_age'] ?? null,
                'children_total' => $familyBackground['children_total'] ?? null,
                'children_boys' => $familyBackground['children_boys'] ?? null,
                'children_girls' => $familyBackground['children_girls'] ?? null,
                'brothers_count' => $familyBackground['brothers_count'] ?? null,
                'sisters_count' => $familyBackground['sisters_count'] ?? null,
                'monthly_income' => $familyBackground['monthly_income'] ?? null,
                'family_type' => $familyBackground['family_type'] ?? null,
                'family_type_other' => $familyBackground['family_type_other'] ?? null,
                'home_ownership_type' => $familyBackground['home_ownership_type'] ?? null,
                'home_ownership_other' => $familyBackground['home_ownership_other'] ?? null,
                'residential_home_type' => $familyBackground['residential_home_type'] ?? null,
                'residential_home_other' => $familyBackground['residential_home_other'] ?? null,
            ],
            'student_information' => [
                'year_level' => $studentInformation['year_level'] ?? null,
                'school_type' => $studentInformation['school_type'] ?? null,
                'last_school_attended' => $studentInformation['last_school_attended'] ?? null,
                'achievement_other_rank' => $studentInformation['achievement_other_rank'] ?? null,
                'achievements' => $this->parseCommaSeparatedList($studentInformation['achievements_text'] ?? null),
                'financing_sources' => $this->parseCommaSeparatedList($studentInformation['financing_sources_text'] ?? null),
                'financing_other_work' => $studentInformation['financing_other_work'] ?? null,
                'gad_training_attended' => isset($studentInformation['gad_training_attended']) ? (bool) $studentInformation['gad_training_attended'] : null,
                'gad_training_details' => $studentInformation['gad_training_details'] ?? null,
            ],
            'other_information' => [
                'emp_skills_hobbies' => $otherInformation['emp_skills_hobbies'] ?? null,
                'emp_distinctions' => $otherInformation['emp_distinctions'] ?? null,
                'emp_membership' => $otherInformation['emp_membership'] ?? null,
            ],
            'employee_education' => $this->normalizeEmployeeRecordRows(
                $validated['employee_education'] ?? $this->parseEmployeeRecord($request->input('employee_education_json')),
                ['level', 'school', 'degree', 'from', 'to'],
                ['level']
            ),
            'civil_service_eligibility' => $this->normalizeEmployeeRecordRows(
                $validated['civil_service_eligibility'] ?? $this->parseEmployeeRecord($request->input('civil_service_eligibility_json')),
                ['career', 'rating', 'exam_date', 'exam_place', 'license_num', 'release_date']
            ),
            'work_experience' => $this->normalizeEmployeeRecordRows(
                $validated['work_experience'] ?? $this->parseEmployeeRecord($request->input('work_experience_json')),
                ['position', 'department', 'from', 'to', 'appointment_status', 'salary', 'grade_step', 'govt_service']
            ),
            'voluntary_work' => $this->normalizeEmployeeRecordRows(
                $validated['voluntary_work'] ?? $this->parseEmployeeRecord($request->input('voluntary_work_json')),
                ['org_name', 'org_address', 'hours', 'position', 'from', 'to']
            ),
            'gad_training' => $this->normalizeEmployeeRecordRows(
                $validated['gad_training'] ?? $this->parseEmployeeRecord($request->input('gad_training_json')),
                ['title', 'from', 'to', 'hours', 'conducted_by']
            ),
        ]);

        if ($request->boolean('return_to_suggestions')) {
            if ($user->hasCompletedPersonalData()) {
                return redirect()->route('student.boses')->with('personal_data_completed', true);
            }

            return redirect()->route('personal-data.edit', ['return_to_suggestions' => 1])
                ->with('status', 'Your personal data was saved. Complete the remaining required fields to access suggestions.');
        }

        return redirect()->route('personal-data.show')->with('status', 'Personal data saved successfully.');
    }

    private function parseCommaSeparatedList(?string $value): array
    {
        if ($value === null) {
            return [];
        }

        $items = preg_split('/[\r\n,]+/', $value) ?: [];

        return array_values(array_filter(array_map(static fn ($item) => trim($item), $items), static fn ($item) => $item !== ''));
    }

    private function parseEmployeeRecord(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : ['text' => trim($value)];
    }

    private function normalizeEmployeeRecordRows(?array $rows, array $fields, array $ignoredForPresence = []): array
    {
        $normalizedRows = [];
        $contentFields = array_diff($fields, $ignoredForPresence);

        foreach ($rows ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $normalized = [];
            foreach ($fields as $field) {
                $value = $row[$field] ?? null;
                if (is_string($value)) {
                    $value = trim($value);
                    $value = $value === '' ? null : $value;
                }
                $normalized[$field] = $value;
            }

            foreach ($contentFields as $field) {
                if ($normalized[$field] !== null) {
                    $normalizedRows[] = $normalized;

                    continue 2;
                }
            }
        }

        return array_values($normalizedRows);
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $oldPhotoPath = $user->profile_photo_path;
        $path = MediaResolver::storeUploadedFile($request->file('profile_photo'), 'profile-photos', false);

        $user->update([
            'profile_photo_path' => $path,
        ]);

        if ($oldPhotoPath && $oldPhotoPath !== $path) {
            MediaResolver::delete($oldPhotoPath, ['public', 'local', config('filesystems.user_public_disk')]);
        }

        return back()->with('status', 'Profile photo updated.');
    }

    public function updateVerificationImage(Request $request): RedirectResponse
    {
        $request->validate([
            'verification_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $user = $request->user();
        $oldIdImagePath = $user->id_image_path;
        $path = MediaResolver::storeUploadedFile($request->file('verification_image'), 'verification/id', true);

        try {
            $user->update(['id_image_path' => $path]);
        } catch (\Throwable $exception) {
            MediaResolver::delete($path);
            throw $exception;
        }

        if ($oldIdImagePath && $oldIdImagePath !== $user->selfie_image_path) {
            MediaResolver::delete($oldIdImagePath);
        }

        return back()->with('status', 'ID verification image updated.');
    }

    public function updateAvailability(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'availability_status' => ['required', Rule::in(['active', 'busy', 'do_not_disturb'])],
        ]);

        $request->user()->update($validated);

        return back()->with('status', 'Availability status updated.');
    }

    public function heartbeat(Request $request)
    {
        $request->user()->forceFill(['last_seen_at' => now()])->saveQuietly();

        return response()->json(['ok' => true]);
    }
}
