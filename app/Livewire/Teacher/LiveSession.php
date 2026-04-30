<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\Resource;
use App\Models\SessionDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LiveSession extends Component
{
    public ClassSession $session;

    public bool $showResourceForm = false;

    public string $resourceTitle = '';

    public string $resourceUrl = '';

    public string $resourceType = 'link';

    public string $announcement = '';

    public bool $showAnnouncement = false;

    public function mount(ClassSession $session): void
    {
        abort_unless(
            $session->classroom->teacher_id === Auth::id(),
            403,
            'You do not own this classroom.'
        );

        abort_if(
            $session->isEnded(),
            403,
            'This session has already ended.'
        );

        $this->session = $session;
    }

    public function endSession(): void
    {
        $this->session->end();

        ActivityLog::record(
            action: 'session.ended',
            description: "Session ended: {$this->session->title}",
            userId: Auth::id(),
            sessionId: $this->session->id,
        );

        $this->redirect(route('teacher.sessions.report', $this->session));
    }

    public function lockAll(): void
    {
        DB::table('session_devices')
            ->where('session_id', $this->session->id)
            ->update(['is_locked' => true]);

        ActivityLog::record(
            action: 'session.devices_locked_all',
            description: 'All devices locked in session.',
            userId: Auth::id(),
            sessionId: $this->session->id,
        );

        session()->flash('message', 'All devices have been locked.');
    }

    public function unlockAll(): void
    {
        DB::table('session_devices')
            ->where('session_id', $this->session->id)
            ->update(['is_locked' => false]);

        ActivityLog::record(
            action: 'session.devices_unlocked_all',
            description: 'All devices unlocked in session.',
            userId: Auth::id(),
            sessionId: $this->session->id,
        );
    }

    public function lockDevice(int $sessionDeviceId): void
    {
        /** @var SessionDevice $sd */
        $sd = SessionDevice::query()->findOrFail($sessionDeviceId);
        $sd->lock();

        ActivityLog::record(
            action: 'session.device_locked',
            description: "Device locked: {$sd->device->name}",
            userId: Auth::id(),
            sessionId: $this->session->id,
        );
    }

    public function unlockDevice(int $sessionDeviceId): void
    {
        /** @var SessionDevice $sd */
        $sd = SessionDevice::query()->findOrFail($sessionDeviceId);
        $sd->unlock();

        ActivityLog::record(
            action: 'session.device_unlocked',
            description: "Device unlocked: {$sd->device->name}",
            userId: Auth::id(),
            sessionId: $this->session->id,
        );
    }

    public function shareResource(): void
    {
        $this->validate([
            'resourceTitle' => 'required|string|min:2',
            'resourceUrl' => 'required|url',
        ]);

        $renderingMode = $this->detectRenderingMode($this->resourceUrl);

        Resource::create([
            'session_id' => $this->session->id,
            'teacher_id' => Auth::id(),
            'title' => $this->resourceTitle,
            'type' => 'link',
            'url' => $this->resourceUrl,
            'rendering_mode' => $renderingMode,
        ]);

        ActivityLog::record(
            action: 'resource.shared',
            description: "Shared resource: {$this->resourceTitle}",
            userId: Auth::id(),
            sessionId: $this->session->id,
        );

        $this->resourceTitle = '';
        $this->resourceUrl = '';
        $this->showResourceForm = false;
    }

    public function pushAnnouncement(): void
    {
        $this->validate([
            'announcement' => 'required|string|min:3|max:255',
        ]);

        ActivityLog::record(
            action: 'session.announcement',
            description: $this->announcement,
            userId: Auth::id(),
            sessionId: $this->session->id,
            metadata: ['announcement' => $this->announcement],
        );

        session()->flash('announcement_sent', 'Announcement logged for this session.');

        $this->announcement = '';
        $this->showAnnouncement = false;
    }

    /**
     * Compute the live duration as a HH:MM:SS string from started_at to now.
     */
    public function getLiveDurationProperty(): string
    {
        if (! $this->session->started_at) {
            return '00:00:00';
        }

        $seconds = (int) $this->session->started_at->diffInSeconds(now());
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    private function detectRenderingMode(string $url): string
    {
        $externalDomains = [
            'google.com',
            'youtube.com',
            'docs.google.com',
            'forms.google.com',
            'drive.google.com',
        ];

        foreach ($externalDomains as $domain) {
            if (str_contains($url, $domain)) {
                return 'external';
            }
        }

        return 'iframe';
    }

    public function render()
    {
        $this->session->loadMissing('classroom');

        $sessionDevices = SessionDevice::query()
            ->where('session_id', $this->session->id)
            ->with('device.user')
            ->orderByRaw('is_locked DESC')
            ->orderBy('joined_at')
            ->get();

        $resources = $this->session->resources()->latest()->get();

        $activityLogs = $this->session->activityLogs()
            ->with('user')
            ->latest('created_at')
            ->take(20)
            ->get();

        $activeDevices = $sessionDevices->whereNull('left_at')->count();
        $violationCount = $sessionDevices->sum('violation_count');
        $lockedCount = $sessionDevices->where('is_locked', true)->count();
        $defaultPolicy = $this->session->classroom->defaultPolicy();

        return view('livewire.teacher.live-session', [
            'sessionDevices' => $sessionDevices,
            'resources' => $resources,
            'activityLogs' => $activityLogs,
            'activeDevices' => $activeDevices,
            'violationCount' => $violationCount,
            'lockedCount' => $lockedCount,
            'defaultPolicy' => $defaultPolicy,
        ]);
    }
}
