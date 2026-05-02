<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\SessionDevice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SessionReport extends Component
{
    public ClassSession $session;

    public function mount(ClassSession $session): void
    {
        abort_unless(
            $session->classroom->teacher_id === Auth::id(),
            403,
            'You do not own this classroom.'
        );

        $this->session = $session;
    }

    public function render()
    {
        // Full session with all needed relations
        $session = ClassSession::query()
            ->with([
                'classroom.teacher',
                'resources' => fn ($q) => $q->oldest(),
                'activityLogs' => fn ($q) => $q->with('user')->oldest('created_at'),
            ])
            ->findOrFail($this->session->id);

        // Session devices with device + student info
        /** @var Collection<int, SessionDevice> $sessionDevices */
        $sessionDevices = SessionDevice::query()
            ->where('session_id', $this->session->id)
            ->with('device.user')
            ->orderByDesc('violation_count')
            ->get();

        // Summary stats
        $totalStudents = $sessionDevices->count();
        $totalViolations = $sessionDevices->sum('violation_count');
        $lockedCount = $sessionDevices->where('is_locked', true)->count();
        $duration = $session->duration();
        $resourcesShared = $session->resources->count();

        // Student with most violations
        $worstOffender = $sessionDevices->sortByDesc('violation_count')->first();

        // Violation breakdown by type from activity_logs metadata
        $violationLogs = ActivityLog::query()
            ->where('session_id', $this->session->id)
            ->where('action', 'focus.violation')
            ->get();

        $violationBreakdown = [
            'tab_switch' => 0,
            'window_blur' => 0,
            'fullscreen_exit' => 0,
        ];

        foreach ($violationLogs as $log) {
            $type = $log->metadata['type'] ?? null;
            if ($type && array_key_exists($type, $violationBreakdown)) {
                $violationBreakdown[$type]++;
            }
        }

        $maxViolationTypeCount = max(1, max($violationBreakdown));

        return view('livewire.teacher.session-report', [
            'session' => $session,
            'sessionDevices' => $sessionDevices,
            'totalStudents' => $totalStudents,
            'totalViolations' => $totalViolations,
            'lockedCount' => $lockedCount,
            'duration' => $duration,
            'resourcesShared' => $resourcesShared,
            'worstOffender' => $worstOffender,
            'violationBreakdown' => $violationBreakdown,
            'maxViolationTypeCount' => $maxViolationTypeCount,
        ]);
    }
}
