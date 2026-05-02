<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\ClassSession;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class ClassroomShow extends Component
{
  public Classroom $classroom;

  // Session creation
  public bool $showSessionForm = false;
  public string $sessionTitle = '';


  public function mount(Classroom $classroom): void
  {
    // Make sure this teacher owns this classroom
    abort_unless(
      $classroom->teacher_id === Auth::id(),
      403
    );

    $this->classroom = $classroom;
  }

  public function startSession(): void
  {
    $this->validate([
      'sessionTitle' => 'required|string|min:3|max:100',
    ]);

    // End any currently active session first
    ClassSession::query()->where('classroom_id', $this->classroom->id)
      ->where('status', 'active')
      ->each(fn(ClassSession $s) => $s->end());

    $session = ClassSession::create([
      'classroom_id' => $this->classroom->id,
      'title' => $this->sessionTitle,
      'status' => 'active',
      'started_at' => now(),
    ]);

    ActivityLog::record(
      action: 'session.started',
      description: "Session started: {$session->title} in {$this->classroom->name}",
      sessionId: $session->id,
    );

    $this->sessionTitle = '';
    $this->showSessionForm = false;

    $this->redirectRoute('teacher.sessions.live', $session);
  }

  public function endSession(int $sessionId): void
  {
    $session = ClassSession::findOrFail($sessionId);
    abort_unless($session->classroom->teacher_id === Auth::id(), 403);

    $session->end();

    ActivityLog::record(
      action: 'session.ended',
      description: "Session ended: {$session->title}",
      sessionId: $session->id,
    );

    $this->dispatch('session-ended');
  }

  public function removeStudent(int $studentId): void
  {
    $this->classroom->students()->detach($studentId);

    $student = User::query()->find($studentId);

    ActivityLog::record(
      action: 'classroom.student_removed',
      description: "Student removed from {$this->classroom->name}: {$student?->name}",
    );

    $this->dispatch('notify', message: "Student removed.", type: 'success');
  }

  public function render()
  {
    $this->classroom->load([
      'students.devices',
      'sessions' => fn($q) => $q->latest()->take(5),
      'policies',
    ]);

    $activeSession = $this->classroom->activeSession();

    return view('livewire.teacher.classroom-show', compact('activeSession'));
  }
}