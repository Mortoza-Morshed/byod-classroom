<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\SessionDevice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class SessionReportController extends Controller
{
    public function exportPdf(ClassSession $session)
    {
        abort_unless(
            $session->classroom->teacher_id === Auth::id(),
            403,
            'You do not own this classroom.'
        );

        // Full session with all needed relations
        $session = ClassSession::query()
            ->with([
                'classroom.teacher',
                'resources' => fn ($q) => $q->oldest(),
                'activityLogs' => fn ($q) => $q->with('user')->oldest('created_at')->take(50), // Limit to 50 entries max
            ])
            ->findOrFail($session->id);

        // Session devices with device + student info
        $sessionDevices = SessionDevice::query()
            ->where('session_id', $session->id)
            ->with('device.user')
            ->orderByDesc('violation_count')
            ->get();

        // Summary stats
        $totalStudents = $sessionDevices->count();
        $totalViolations = $sessionDevices->sum('violation_count');
        $lockedCount = $sessionDevices->where('is_locked', true)->count();
        $duration = $session->duration();
        $resourcesShared = $session->resources->count();

        // Violation breakdown by type from activity_logs metadata
        $violationLogs = ActivityLog::query()
            ->where('session_id', $session->id)
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

        $data = [
            'session' => $session,
            'sessionDevices' => $sessionDevices,
            'totalStudents' => $totalStudents,
            'totalViolations' => $totalViolations,
            'lockedCount' => $lockedCount,
            'duration' => $duration,
            'resourcesShared' => $resourcesShared,
            'violationBreakdown' => $violationBreakdown,
        ];

        $pdf = Pdf::loadView('pdf.session-report', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'session-report-'.$session->id.'-'.date('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }
}
