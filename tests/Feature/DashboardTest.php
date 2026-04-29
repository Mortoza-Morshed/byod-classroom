<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('home'));
    $response->assertRedirect(route('login'));
});

test('authenticated users are redirected to their role dashboard', function () {
    /** @var \Tests\TestCase $this */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student']);
    // Assign student role to the user so the fallback works cleanly
    $user->assignRole('student');
    $this->actingAs($user);

    $response = $this->get(route('home'));
    $response->assertRedirect(route('student.dashboard'));
});