<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\SuggestionApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SINAG mobile API  (prefix: /api)
|--------------------------------------------------------------------------
| Token auth via Laravel Sanctum. Used by the Flutter app (sinag_mobile).
| Admin/GAD-office tools stay on the web dashboard.
*/

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

Route::middleware(['auth:sanctum', 'account.active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/presence/heartbeat', [AuthController::class, 'heartbeat']);
    Route::post('/profile/photo', [AuthController::class, 'updatePhoto']);
    Route::post('/profile/availability', [AuthController::class, 'updateAvailability']);

    // Reports (file a report, view own vault)
    Route::post('/reports', [ReportApiController::class, 'store']);
    Route::get('/reports', [ReportApiController::class, 'index']);
    Route::get('/reports/{report}/evidence/{index}', [ReportApiController::class, 'evidence'])->whereNumber('index');

    // Suggestions wall (Boses)
    Route::get('/suggestions', [SuggestionApiController::class, 'index']);
    Route::get('/suggestions/mine', [SuggestionApiController::class, 'mine']);
    Route::post('/suggestions', [SuggestionApiController::class, 'store']);
    Route::put('/suggestions/{id}', [SuggestionApiController::class, 'update']);
    Route::delete('/suggestions/{id}', [SuggestionApiController::class, 'destroy']);
    Route::post('/suggestions/{id}/upvote', [SuggestionApiController::class, 'upvote']);
    Route::post('/suggestions/{id}/comments', [SuggestionApiController::class, 'comment']);
    Route::put('/comments/{id}', [SuggestionApiController::class, 'updateComment']);
    Route::delete('/comments/{id}', [SuggestionApiController::class, 'destroyComment']);
    Route::post('/suggestions/{id}/report', [SuggestionApiController::class, 'reportSuggestion']);
    Route::post('/comments/{id}/report', [SuggestionApiController::class, 'reportComment']);

    // Student / employee features
    Route::middleware('can:student-access')->group(function () {
        Route::get('/dashboard', [StudentApiController::class, 'dashboard']);
        Route::get('/announcements/{id}', [StudentApiController::class, 'announcement']);
        Route::get('/gad-schedules', [StudentApiController::class, 'gadSchedules']);
        Route::get('/references', [StudentApiController::class, 'references']);
        Route::get('/references/{slug}', [StudentApiController::class, 'reference']);

        Route::get('/appointments', [StudentApiController::class, 'appointments']);
        Route::post('/appointments', [StudentApiController::class, 'storeAppointment']);
        Route::put('/appointments/{id}', [StudentApiController::class, 'updateAppointment']);
        Route::post('/appointments/{id}/cancel', [StudentApiController::class, 'cancelAppointment']);

        Route::get('/messages', [StudentApiController::class, 'messages']);
        Route::post('/messages', [StudentApiController::class, 'storeMessage']);
        Route::put('/messages/{id}', [StudentApiController::class, 'updateMessage']);
        Route::delete('/messages/{id}', [StudentApiController::class, 'destroyMessage']);

        Route::post('/urgent/notify', [StudentApiController::class, 'urgentNotify']);
    });
});
