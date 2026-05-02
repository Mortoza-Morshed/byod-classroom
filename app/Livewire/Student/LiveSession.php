<?php

namespace App\Livewire\Student;

use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\Device;
use App\Models\Resource;
use App\Models\SessionDevice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LiveSession extends Component
{
    public ClassSession $session;

    public ?SessionDevice $sessionDevice = null;

    public ?Device $device = null;

    public ?int $activeResourceId = null;

    public bool $focusPaused = false;

    public int $focusPausedSeconds = 0;

    public function mount(ClassSession $session): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Verify enrollment
        abort_unless(
            $user->enrolledClassrooms()->where('classroom_id', $session->classroom_id)->exists(),
            403,
            'You are not enrolled in this classroom.'
        );

        // If session has ended, redirect back to classroom
        if ($session->isEnded()) {
            session()->flash('message', 'Session has ended.');
            $this->redirect(route('student.classrooms.show', $session->classroom_id));

            return;
        }

        // Find approved device
        $this->device = $user->approvedDevice();
        abort_unless($this->device !== null, 403, 'No approved device registered.');

        // Find or create SessionDevice record
        $this->sessionDevice = SessionDevice::firstOrCreate(
            [
                'session_id' => $session->id,
                'device_id' => $this->device->id,
            ],
            [
                'joined_at' => now(),
            ]
        );

        ActivityLog::record(
            action: 'session.student_joined',
            description: "{$user->name} joined the session.",
            userId: $user->id,
            deviceId: $this->device->id,
            sessionId: $session->id,
        );

        $this->session = $session;
    }

    #[Computed]
    public function sessionData(): ClassSession
    {
        return ClassSession::query()
            ->with([
                'classroom.teacher',
                'resources' => fn ($q) => $q->latest(),
            ])
            ->findOrFail($this->session->id);
    }

    #[Computed]
    public function announcements()
    {
        return ActivityLog::query()
            ->where('session_id', $this->session->id)
            ->where('action', 'session.announcement')
            ->latest('created_at')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function policy(): ?object
    {
        return $this->session->classroom->defaultPolicy();
    }

    #[Computed]
    public function isLocked(): bool
    {
        $sd = $this->sessionDevice?->fresh();

        if (! $sd || ! $sd->is_locked) {
            return false;
        }

        // Auto-unlock if a timed lock has expired
        if ($sd->locked_until !== null && $sd->locked_until->isPast()) {
            $sd->unlock();

            return false;
        }

        return true;
    }

    public function openResource(int $resourceId): void
    {
        $this->activeResourceId = $resourceId;
    }

    public function pauseFocus(int $seconds): void
    {
        $this->focusPaused = true;
        $this->focusPausedSeconds = $seconds;

        ActivityLog::record(
            action: 'session.focus_paused',
            description: "Focus monitoring paused for {$seconds} seconds.",
            userId: Auth::id(),
            deviceId: $this->device?->id,
            sessionId: $this->session->id,
            metadata: ['seconds' => $seconds],
        );
    }

    public function reportViolation(string $type): ?int
    {
        if ($this->focusPaused) {
            return null;
        }

        // Reload fresh session to check status
        $freshSession = ClassSession::query()->find($this->session->id);
        if (! $freshSession || ! $freshSession->isActive()) {
            return null;
        }

        $sd = $this->sessionDevice?->fresh();
        if (! $sd) {
            return null;
        }

        // Do not record violations while the device is already locked
        if ($sd->is_locked) {
            return null;
        }

        $sd->incrementViolation();
        $warningLevel = $sd->warningLevel();

        $messages = [
            1 => 'Please stay focused on the session.',
            2 => 'One more violation will lock your device for 10 seconds.',
            3 => 'Device locked for 10 seconds due to repeated violations.',
        ];
        $message = $messages[min($warningLevel, 3)] ?? $messages[3];

        // Auto-lock for 10 seconds every time the violation count hits a multiple of 3
        if ($warningLevel >= 3) {
            $sd->lockFor(10);
        }

        ActivityLog::record(
            action: 'focus.violation',
            description: "Focus violation ({$type}) — warning level {$warningLevel}.",
            userId: Auth::id(),
            deviceId: $this->device?->id,
            sessionId: $this->session->id,
            metadata: ['type' => $type, 'warning_level' => $warningLevel, 'auto_locked' => $warningLevel >= 3],
        );

        $this->dispatch('violation-recorded', level: $warningLevel, message: $message);

        return $warningLevel;
    }

    public function render()
    {
        // Redirect if session ended during the view lifecycle
        $freshSession = ClassSession::query()->find($this->session->id);
        if ($freshSession && $freshSession->isEnded()) {
            session()->flash('message', 'Session has ended.');

            return $this->redirect(route('student.classrooms.show', $this->session->classroom_id));
        }

        // Reload sessionDevice for fresh lock status
        $this->sessionDevice = $this->sessionDevice
            ? $this->sessionDevice->fresh()
            : null;

        /** @var resource|null $activeResource */
        $activeResource = $this->activeResourceId
            ? Resource::query()->find($this->activeResourceId)
            : null;

        return view('livewire.student.live-session', [
            'isLocked' => $this->isLocked,
            'policy' => $this->policy,
            'sessionData' => $this->sessionData,
            'announcements' => $this->announcements,
            'activeResource' => $activeResource,
        ]);
    }
}
