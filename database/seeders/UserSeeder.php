<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name'      => 'School Admin',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // ── Teachers ─────────────────────────────────────────────
        $teachers = [
            ['name' => 'Mr. Sharma',  'email' => 'sharma@school.com'],
            ['name' => 'Ms. Priya',   'email' => 'priya@school.com'],
            ['name' => 'Mr. Verma',   'email' => 'verma@school.com'],
        ];

        foreach ($teachers as $data) {
            $teacher = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $teacher->assignRole('teacher');
        }

        // ── Students ─────────────────────────────────────────────
        $students = [
            'Aarav Singh',    'Ananya Gupta',   'Rohan Mehta',
            'Priya Sharma',   'Arjun Patel',    'Sneha Rao',
            'Kabir Nair',     'Ishaan Joshi',   'Diya Kapoor',
            'Vivaan Kumar',   'Anika Reddy',    'Advait Mishra',
            'Saanvi Iyer',    'Reyansh Das',    'Myra Tiwari',
        ];

        foreach ($students as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@student.com';

            $student = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $name,
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $student->assignRole('student');
        }

        $this->command->info('Users created: 1 admin, 3 teachers, 15 students');
    }
}