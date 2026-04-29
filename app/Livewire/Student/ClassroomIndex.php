<?php

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClassroomIndex extends Component
{
    public function render()
    {
        $classrooms = Auth::user()
            ->enrolledClassrooms()
            ->with('teacher')
            ->withCount('students')
            ->with(['sessions' => fn ($query) => $query->where('status', 'active')->latest()])
            ->latest('classroom_student.joined_at')
            ->get();

        return view('livewire.student.classroom-index', compact('classrooms'));
    }
}
