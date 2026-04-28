<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionViolationController;
use Illuminate\Support\Facades\Auth;
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
        return redirect()->route(Auth::user()->getRoleNames()->first() . '.dashboard');
    }
    return redirect()->route('login');
})->name('home');

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

        // User management
        Route::get('/users', function () {
            return view('pages.admin.users.index');
        })->name('users.index');

        Route::get('/users/{user}', function () {
            return view('pages.admin.users.show');
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
    ->middleware(['auth', 'role:teacher'])
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

        Route::get('/classrooms/{classroom}', function (App\Models\Classroom $classroom) {
            return view('pages.teacher.classrooms.show', compact('classroom'));
        })->name('classrooms.show');

        // Sessions
        Route::get('/classrooms/{classroom}/sessions/create', function (App\Models\Classroom $classroom) {
            return view('pages.teacher.sessions.create', compact('classroom'));
        })->name('sessions.create');

        Route::get('/sessions/{session}/live', function () {
            return view('pages.teacher.sessions.live');
        })->name('sessions.live');

        Route::get('/sessions/{session}/report', function () {
            return view('pages.teacher.sessions.report');
        })->name('sessions.report');

        // Devices
        Route::get('/devices', function () {
            return view('pages.teacher.devices.index');
        })->name('devices.index');

        // Policies
        Route::get('/classrooms/{classroom}/policies', function () {
            return view('pages.teacher.policies.index');
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

        Route::get('/classrooms/{classroom}', function () {
            return view('pages.student.classrooms.show');
        })->name('classrooms.show');

        // Join a classroom via code
        Route::get('/join', function () {
            return view('pages.student.join');
        })->name('join');

        // Active session view
        Route::get('/sessions/{session}/live', function () {
            return view('pages.student.sessions.live');
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
            'store'
        ])->name('violation');

        // Device lock status polling (called by student page)
        Route::get('/{session}/lock-status', [
            SessionViolationController::class,
            'lockStatus'
        ])->name('lock-status');
    });

/*
|--------------------------------------------------------------------------
| Auth routes (provided by the Livewire starter kit)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';