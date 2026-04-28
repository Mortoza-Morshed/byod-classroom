<?php

namespace App\Livewire\Teacher;

use App\Models\Classroom;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class ClassroomIndex extends Component
{
    public function render()
    {
        $classrooms = Auth::user()
            ->classrooms()
            ->withCount('students')
            ->with(['sessions' => fn($q) => $q->where('status', 'active')])
            ->latest()
            ->get();

        return view('livewire.teacher.classroom-index', compact('classrooms'));
    }
}