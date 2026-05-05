<?php

use App\Http\Controllers\SessionViolationController;
use App\Http\Controllers\Teacher\SessionReportController;
use App\Models\Classroom;
use App\Models\ClassSession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// TEMPORARY - remove before production
Route::get('/dev-logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
});

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->getRoleNames()->first() ?? 'student';

        return redirect()->route($role.'.dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pages.admin.dashboard');
        })->name('dashboard');

        Route::get('/users', function () {
            return view('pages.admin.users.index');
        })->name('users.index');

        Route::get('/users/{user}', function (\App\Models\User $user) {
            return view('pages.admin.users.show', compact('user'));
        })->name('users.show');

        // Device management
        Route::get('/devices', function () {
            return view('pages.admin.devices.index');
        })->name('devices.index');

        // Audit logs
        Route::get('/logs', function () {
            return view('pages.admin.logs.index');
        })->name('logs.index');

        // Reports
        Route::get('/reports', function () {
            return view('pages.admin.reports.index');
        })->name('reports.index');
    });

/*
|--------------------------------------------------------------------------
| Teacher routes
|--------------------------------------------------------------------------
*/

Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'role:teacher|admin'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pages.teacher.dashboard');
        })->name('dashboard');

        // Classrooms
        Route::get('/classrooms', function () {
            return view('pages.teacher.classrooms.index');
        })->name('classrooms.index');

        Route::get('/classrooms/create', function () {
            return view('pages.teacher.classrooms.create');
        })->name('classrooms.create');

        Route::get('/classrooms/{classroom}', function (Classroom $classroom) {
            return view('pages.teacher.classrooms.show', compact('classroom'));
        })->name('classrooms.show');

        // Sessions
        Route::get('/classrooms/{classroom}/sessions/create', function (Classroom $classroom) {
            return view('pages.teacher.sessions.create', compact('classroom'));
        })->name('sessions.create');

        Route::get('/sessions/{session}/live', function (ClassSession $session) {
            return view('pages.teacher.sessions.live', compact('session'));
        })->name('sessions.live');

        Route::get('/sessions/{session}/report', function (ClassSession $session) {
            return view('pages.teacher.sessions.report', compact('session'));
        })->name('sessions.report');

        Route::get('/sessions/{session}/report/pdf',
            [SessionReportController::class, 'exportPdf']
        )->name('sessions.report.pdf');

        // Devices
        Route::get('/devices', function () {
            return view('pages.teacher.devices.index');
        })->name('devices.index');

        // Policies
        Route::get('/classrooms/{classroom}/policies', function (Classroom $classroom) {
            return view('pages.teacher.policies.index', compact('classroom'));
        })->name('policies.index');
    });

/*
|--------------------------------------------------------------------------
| Student routes
|--------------------------------------------------------------------------
*/

Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pages.student.dashboard');
        })->name('dashboard');

        // Classrooms
        Route::get('/classrooms', function () {
            return view('pages.student.classrooms.index');
        })->name('classrooms.index');

        Route::get('/classrooms/{classroom}', function (Classroom $classroom) {
            return view('pages.student.classrooms.show', compact('classroom'));
        })->name('classrooms.show');

        // Join a classroom via code
        Route::get('/join', function () {
            return view('pages.student.join');
        })->name('join');

        // Active session view
        Route::get('/sessions/{session}/live', function (ClassSession $session) {
            return view('pages.student.sessions.live', compact('session'));
        })->name('sessions.live');

        // Device registration
        Route::get('/device', function () {
            return view('pages.student.device');
        })->name('device');
    });

/*
|--------------------------------------------------------------------------
| Shared API-style routes (used by Livewire + JS during sessions)
|--------------------------------------------------------------------------
*/

Route::prefix('api/session')
    ->name('session.api.')
    ->middleware(['auth'])
    ->group(function () {

        // Focus violation reporting (called by JS on tab switch)
        Route::post('/violation', [
            SessionViolationController::class,
            'store',
        ])->name('violation');

        // Device lock status polling (called by student page)
        Route::get('/{session}/lock-status', [
            SessionViolationController::class,
            'lockStatus',
        ])->name('lock-status');
    });

/*
|--------------------------------------------------------------------------
| Auth routes (provided by the Livewire starter kit)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
