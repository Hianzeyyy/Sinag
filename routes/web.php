<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuggestionReportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\PasswordResetRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes - SINAG Project (Violet Edition)
|--------------------------------------------------------------------------
*/

// 1. Splash Screen
Route::get('/', function () {
    return view('splash'); 
})->name('splash');

// 2. Auth Routes
Auth::routes(['register' => true, 'reset' => false]);
Route::get('/password/request', [ForgotPasswordController::class, 'showRequestForm'])->name('password.code.form');
Route::post('/password/request', [ForgotPasswordController::class, 'storeRequest'])->middleware('throttle:password-reset-request')->name('password.code.request');
Route::get('/password/status', [ForgotPasswordController::class, 'showStatusForm'])->name('password.status.form');
Route::post('/password/status', [ForgotPasswordController::class, 'checkStatus'])->middleware('throttle:password-reset-status')->name('password.status.check');
Route::get('/password/reset/{passwordResetRequest}/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/password/reset/{passwordResetRequest}/{token}', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:password-reset-submit')->name('password.reset.submit');

// 3. Central Redirect Logic (Ito ang traffic control pagka-login)
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::middleware(['auth'])->get('/personal-data', [ProfileController::class, 'personalData'])->name('personal-data.show');
Route::middleware(['auth'])->get('/personal-data/edit', [ProfileController::class, 'editPersonalData'])->name('personal-data.edit');

// --- AUTHENTICATED USERS ONLY ---
Route::middleware(['auth'])->group(function () {

    // --- ADMIN ROUTES ---
    Route::prefix('admin')->name('admin.')->middleware('can:admin-access')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/password-reset-requests', [PasswordResetRequestController::class, 'index'])->name('password-resets.index');
        Route::get('/password-reset-requests/{passwordResetRequest}', [PasswordResetRequestController::class, 'show'])->name('password-resets.show');
        Route::get('/password-reset-requests/{passwordResetRequest}/id-picture', [PasswordResetRequestController::class, 'idPicture'])->name('password-resets.id-picture');
        Route::post('/password-reset-requests/{passwordResetRequest}/approve', [PasswordResetRequestController::class, 'approve'])->name('password-resets.approve');
        Route::post('/password-reset-requests/{passwordResetRequest}/reject', [PasswordResetRequestController::class, 'reject'])->name('password-resets.reject');
        
        // GAD Office Tools
        Route::get('/reports-management', [AdminController::class, 'reports'])->name('reports');
        Route::get('/suggestions', [AdminController::class, 'suggestions'])->name('suggestions');
        Route::get('/suggestions/reports', [AdminController::class, 'suggestionReports'])->name('suggestions.reports');
        Route::delete('/suggestions/reports/{suggestionReport}', [AdminController::class, 'deleteReportedSuggestion'])->name('suggestions.reports.delete');
        Route::post('/suggestions/reports/{suggestionReport}/dismiss', [AdminController::class, 'dismissSuggestionReport'])->name('suggestions.reports.dismiss');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
        Route::post('/messages/store', [AdminController::class, 'storeMessage'])->name('messages.store');
        Route::get('/gad-schedules', [AdminController::class, 'gadSchedules'])->name('gad-schedules.index');
        Route::post('/gad-schedules/sync', [AdminController::class, 'syncGadSchedule'])->name('gad-schedules.sync');
        Route::post('/gad-schedules', [AdminController::class, 'storeGadSchedule'])->name('gad-schedules.store');
        Route::put('/gad-schedules/{id}', [AdminController::class, 'updateGadSchedule'])->name('gad-schedules.update');
        Route::post('/gad-schedules/{id}/status', [AdminController::class, 'updateGadScheduleStatus'])->name('gad-schedules.status');
        Route::get('/urgent-calls', [AdminController::class, 'urgentCalls'])->name('urgent.calls');
        Route::get('/urgent-calls/feed', [AdminController::class, 'urgentCallsFeed'])->name('urgent.calls.feed');
        Route::post('/urgent-calls/{callId}/join', [AdminController::class, 'joinUrgentCall'])->name('urgent.calls.join');
        Route::post('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/users/{id}/view', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{id}/personal-data', [ProfileController::class, 'adminPersonalData'])->name('users.personal-data');
        
        // Appointment Management
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{id}/complete', [AdminController::class, 'completeAppointment'])->name('appointments.complete');
        Route::post('/appointments/{id}/cancel', [AdminController::class, 'cancelAppointment'])->name('appointments.cancel');
        Route::post('/appointments/{id}/add-details', [AdminController::class, 'addAppointmentDetails'])->name('appointments.add-details');
        
        // Admin Actions
        Route::post('/reports/{id}/update', [AdminController::class, 'updateStatus'])->name('reports.update');
        Route::post('/reports/{id}/seen', [AdminController::class, 'markSeen'])->name('reports.seen');
        Route::get('/reports/{id}/pdf', [AdminController::class, 'downloadReportPdf'])->name('reports.pdf');
        Route::post('/suggestions/{id}/review', [AdminController::class, 'reviewSuggestion'])->name('suggestions.review');
        Route::post('/reveal-identity/{report_id}', [AdminController::class, 'requestReveal'])->name('reveal');
        Route::delete('/reports/{id}/delete', [AdminController::class, 'deleteReport'])->name('reports.delete');

        // Announcement Management
        Route::resource('announcements', App\Http\Controllers\Admin\AnnouncementController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // --- STUDENT ROUTES ---
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'index'])->name('dashboard');
        
        // Core Modules
        Route::get('/report', [StudentController::class, 'report'])->name('report'); 
        Route::get('/vault', [StudentController::class, 'vault'])->name('vault');   
        Route::middleware('profile.complete')->group(function () {
            Route::get('/boses', [StudentController::class, 'boses'])->name('boses');
            Route::get('/gad-schedule', [StudentController::class, 'gadSchedule'])->name('gad-schedule');
            Route::get('/schedule-appointment', [StudentController::class, 'appointmentForm'])->name('schedule-appointment');
            Route::post('/schedule-appointment', [StudentController::class, 'storeAppointment'])->name('schedule-appointment.store');
            Route::get('/appointments', [StudentController::class, 'myAppointments'])->name('appointments');
            Route::put('/appointments/{id}', [StudentController::class, 'updateAppointment'])->name('appointments.update');
            Route::post('/appointments/{id}/cancel', [StudentController::class, 'cancelAppointment'])->name('appointments.cancel');
        });
        
        // Safety & Wellness
        Route::get('/wellness', [StudentController::class, 'wellness'])->name('wellness'); 
        Route::get('/reference/{slug}', [StudentController::class, 'reference'])->name('reference');
        
        // Messaging & Communication
        Route::get('/messaging', [StudentController::class, 'messaging'])->name('messaging');
        Route::post('/messages/store', [StudentController::class, 'storeMessage'])->name('messages.store');
        Route::post('/messages/{id}/update', [StudentController::class, 'updateMessage'])->name('messages.update');
        Route::delete('/messages/{id}', [StudentController::class, 'destroyMessage'])->name('messages.destroy');
        Route::get('/announcement/{id}', [StudentController::class, 'announcement'])->name('announcement');
        // Urgent video/voice call (placeholder Zoom-like page)
        Route::get('/urgent', [StudentController::class, 'urgentCall'])->name('urgent');
        Route::post('/urgent/notify', [StudentController::class, 'urgentNotify'])->name('urgent.notify');
        // Event pages for carousel items
        Route::get('/event/{id}', [StudentController::class, 'event'])->name('event');
    });

    // --- SHARED ACTIONS ---
    Route::post('/submit-report', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}/evidence/{index}', [ReportController::class, 'viewEvidence'])
        ->whereNumber('index')
        ->name('reports.evidence.view');
    Route::get('/suggestions/{suggestion}', [SuggestionController::class, 'show'])->name('suggestions.show');
    Route::middleware('profile.complete')->group(function () {
        Route::post('/submit-suggestion', [SuggestionController::class, 'store'])->name('suggestions.store');
        Route::post('/upvote-suggestion/{id}', [SuggestionController::class, 'upvote'])->name('suggestions.upvote');
        Route::post('/suggestions/{suggestion}/comment', [App\Http\Controllers\SuggestionCommentController::class, 'store'])->name('suggestions.comment');
        Route::put('/suggestions/{id}', [SuggestionController::class, 'update'])->name('suggestions.update');
        Route::delete('/suggestions/{id}', [SuggestionController::class, 'destroy'])->name('suggestions.destroy');
        Route::post('/suggestions/{suggestion}/report', [SuggestionReportController::class, 'storePost'])->name('suggestions.report');
        Route::put('/comments/{id}', [App\Http\Controllers\SuggestionCommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{id}', [App\Http\Controllers\SuggestionCommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/comments/{comment}/report', [SuggestionReportController::class, 'storeComment'])->name('comments.report');
    });
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::post('/profile/availability', [ProfileController::class, 'updateAvailability'])->name('profile.availability.update');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/presence/heartbeat', [ProfileController::class, 'heartbeat'])->name('presence.heartbeat');
});