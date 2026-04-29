<?php

namespace App\Livewire\Student;

use App\Models\ActivityLog;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinClassroom extends Component
{
    public string $joinCode = '';

    public function join(): void
    {
        $this->joinCode = strtoupper(trim($this->joinCode));

        $this->validate([
            'joinCode' => 'required|string|size:6|exists:classrooms,join_code',
        ]);

        $classroom = Classroom::query()->where('join_code', $this->joinCode)->firstOrFail();

        if (! $classroom->is_active) {
            $this->addError('joinCode', 'This classroom is currently inactive and cannot be joined.');

            return;
        }

        $user = Auth::user();

        if ($user->enrolledClassrooms()->where('classroom_id', $classroom->id)->exists()) {
            $this->addError('joinCode', 'You are already enrolled in this classroom.');

            return;
        }

        // Enroll the student
        $user->enrolledClassrooms()->attach($classroom->id, ['joined_at' => now()]);

        ActivityLog::record(
            action: 'classroom.joined',
            description: "Joined classroom: {$classroom->name}",
        );

        session()->flash('success', 'Successfully joined the classroom!');

        $this->redirectRoute('student.classrooms.show', $classroom);
    }

    public function render()
    {
        return view('livewire.student.join-classroom');
    }
}
