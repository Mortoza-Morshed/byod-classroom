<?php

namespace App\Livewire\Student;

use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\SessionDevice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SessionSummary extends Component
{
    public ClassSession $classSession;
    public ?SessionDevice $sessionDevice = null;

    public function mount(ClassSession $classSession): void
    {
        /** @var User $user */
        $user = Auth::user();
        $device = $user->approvedDevice();

        if (!$device) {
            abort(403, 'No approved device found.');
        }

        $this->sessionDevice = SessionDevice::where('session_id', $classSession->id)
            ->where('device_id', $device->id)
            ->first();

        if (!$this->sessionDevice) {
            abort(403, 'You did not participate in this session.');
        }

        $this->classSession = $classSession;
    }

    #[Computed]
    public function sessionData(): ClassSession
    {
        return ClassSession::query()
            ->with(['classroom.teacher', 'resources' => fn($q) => $q->oldest()])
            ->findOrFail($this->classSession->id);
    }

    #[Computed]
    public function violations()
    {
        return ActivityLog::query()
            ->where('session_id', $this->classSession->id)
            ->where('user_id', Auth::id())
            ->where('action', 'focus.violation')
            ->oldest()
            ->get();
    }

    #[Computed]
    public function announcements()
    {
        return ActivityLog::query()
            ->where('session_id', $this->classSession->id)
            ->where('action', 'session.announcement')
            ->oldest()
            ->get();
    }

    #[Computed]
    public function timeInSession(): string
    {
        $start = $this->sessionDevice->joined_at;
        $end = $this->sessionDevice->left_at ?? $this->classSession->ended_at ?? now();

        if (!$start) return '0 minutes';

        $diffInMinutes = $start->diffInMinutes($end);

        if ($diffInMinutes < 60) {
            return "{$diffInMinutes} minutes";
        }

        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        return $minutes > 0 ? "{$hours} hours {$minutes} minutes" : "{$hours} hours";
    }

    #[Computed]
    public function focusScore(): array
    {
        $count = $this->sessionDevice->violation_count;

        return match (true) {
            $count === 0 => ['label' => 'Excellent', 'color' => 'emerald'],
            $count === 1 => ['label' => 'Good', 'color' => 'blue'],
            $count === 2 => ['label' => 'Fair', 'color' => 'amber'],
            default => ['label' => 'Needs Improvement', 'color' => 'red'],
        };
    }

    public function render()
    {
        return view('livewire.student.session-summary', [
            'session' => $this->sessionData,
            'violations' => $this->violations,
            'announcements' => $this->announcements,
            'timeInSession' => $this->timeInSession,
            'focusScore' => $this->focusScore,
        ]);
    }
}
