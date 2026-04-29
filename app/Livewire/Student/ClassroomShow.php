<?php

namespace App\Livewire\Student;

use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClassroomShow extends Component
{
    public Classroom $classroom;

    public function mount(Classroom $classroom): void
    {
        $user = Auth::user();

        // Verify the student is actually enrolled in this classroom
        abort_unless(
            $user->enrolledClassrooms()->where('classroom_id', $classroom->id)->exists(),
            403,
            'You are not enrolled in this classroom.'
        );

        $this->classroom = $classroom->load('teacher');
    }

    public function render()
    {
        $this->classroom->load([
            'sessions' => fn ($q) => $q->latest()->take(5),
        ]);

        $activeSession = $this->classroom->activeSession();
        /** @var \App\Models\ClassSession|null $recentSession */
        $recentSession = $this->classroom->sessions()->latest()->first();

        $resources = collect();
        if ($recentSession) {
            $resources = $recentSession->resources()->latest()->get();
        }

        return view('livewire.student.classroom-show', [
            'activeSession' => $activeSession,
            'recentSession' => $recentSession,
            'resources' => $resources,
        ]);
    }
}
