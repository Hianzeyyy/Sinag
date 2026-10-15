<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $verificationImagePath = $request->user()->id_image_path ?: $request->user()->selfie_image_path;
        $verificationImageAvailable = false;

        if ($verificationImagePath) {
            foreach (['local', 'public'] as $diskName) {
                if (Storage::disk($diskName)->exists($verificationImagePath)) {
                    $verificationImageAvailable = true;
                    break;
                }
            }
        }

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
            'user' => \App\Models\User::findOrFail($id),
            'adminViewer' => true,
        ]);
    }

    public function verificationImage(Request $request, \App\Models\User $user)
    {
        abort_unless(
            $request->user()->role === 'admin' || $request->user()->is($user),
            403
        );

        $path = $user->id_image_path ?: $user->selfie_image_path;
        abort_if(blank($path), 404);

        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);

            if ($disk->exists($path)) {
                return response()->file($disk->path($path));
            }
        }

        abort(404);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isEmployee = strtolower((string) ($user->account_type ?? $user->role)) === 'employee';

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'department' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/'],
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
            'family_background.father_age' => ['nullable', 'string', 'max:50'],
            'family_background.mother_name' => ['nullable', 'string', 'max:255'],
            'family_background.mother_occupation' => ['nullable', 'string', 'max:255'],
            'family_background.mother_age' => ['nullable', 'string', 'max:50'],
            'family_background.children_total' => ['nullable', 'string', 'max:50'],
            'family_background.children_boys' => ['nullable', 'string', 'max:50'],
            'family_background.children_girls' => ['nullable', 'string', 'max:50'],
            'family_background.brothers_count' => ['nullable', 'string', 'max:50'],
            'family_background.sisters_count' => ['nullable', 'string', 'max:50'],
            'family_background.monthly_income' => ['nullable', 'string', 'max:100'],
            'family_background.family_type' => ['nullable', 'string', 'max:100'],
            'family_background.family_type_other' => ['nullable', 'string', 'max:255'],
            'family_background.home_ownership_type' => ['nullable', 'string', 'max:100'],
            'family_background.home_ownership_other' => ['nullable', 'string', 'max:255'],
            'family_background.residential_home_type' => ['nullable', 'string', 'max:100'],
            'family_background.residential_home_other' => ['nullable', 'string', 'max:255'],
            'student_information' => [$isEmployee ? 'nullable' : 'required', 'array'],
            'student_information.year_level' => [Rule::requiredIf(!$isEmployee), 'nullable', 'string', 'max:100'],
            'student_information.school_type' => [Rule::requiredIf(!$isEmployee), 'nullable', 'string', 'max:100'],
            'student_information.last_school_attended' => [Rule::requiredIf(!$isEmployee), 'nullable', 'string', 'max:255'],
            'student_information.achievement_other_rank' => ['nullable', 'string', 'max:255'],
            'student_information.achievements_text' => ['nullable', 'string', 'max:1000'],
            'student_information.financing_sources_text' => [Rule::requiredIf(!$isEmployee), 'nullable', 'string', 'max:1000'],
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
            'email' => $validated['email'],
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
            'employee_education' => $this->parseEmployeeRecord($request->input('employee_education_json')),
            'civil_service_eligibility' => $this->parseEmployeeRecord($request->input('civil_service_eligibility_json')),
            'work_experience' => $this->parseEmployeeRecord($request->input('work_experience_json')),
            'voluntary_work' => $this->parseEmployeeRecord($request->input('voluntary_work_json')),
            'gad_training' => $this->parseEmployeeRecord($request->input('gad_training_json')),
        ]);

        if ($request->boolean('return_to_suggestions')) {
            if ($user->hasCompletedPersonalData()) {
                return redirect()->route('student.boses')->with('personal_data_completed', true);
            }

            return redirect()->route('personal-data.edit', ['return_to_suggestions' => 1])
                ->with('status', 'Profile updated. Complete the remaining required personal data to access suggestions.');
        }

        return back()->with('status', $user->hasCompletedPersonalData()
            ? 'Personal information complete. Suggestions, Schedule, and the other features are now available.'
            : 'Profile information updated.');
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

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $path,
        ]);

        return back()->with('status', 'Profile photo updated.');
    }

    public function updateVerificationImage(Request $request): RedirectResponse
    {
        $request->validate([
            'verification_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $user = $request->user();
        $oldIdImagePath = $user->id_image_path;
        $path = $request->file('verification_image')->store('verification/id', 'local');

        if (!is_string($path)) {
            Log::error('Verification image upload failed', ['user_id' => $user->id]);

            return back()->withErrors([
                'verification_image' => 'The ID image could not be saved. Please try again.',
            ]);
        }

        try {
            $user->update(['id_image_path' => $path]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        if ($oldIdImagePath && $oldIdImagePath !== $user->selfie_image_path) {
            foreach (['local', 'public'] as $diskName) {
                Storage::disk($diskName)->delete($oldIdImagePath);
            }
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
