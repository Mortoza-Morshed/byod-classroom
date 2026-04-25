<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect the user to their role-specific dashboard after login.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): RedirectResponse
    {
        $user = Auth::user();

        $destination = match (true) {
            $user->hasRole('admin')   => route('admin.dashboard'),
            $user->hasRole('teacher') => route('teacher.dashboard'),
            $user->hasRole('student') => route('student.dashboard'),
            default                   => route('home'),
        };

        return redirect()->intended($destination);
    }
}
