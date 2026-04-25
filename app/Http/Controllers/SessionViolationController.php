<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\SessionDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionViolationController extends Controller
{
    // Called by JavaScript when a tab switch / blur / fullscreen exit is detected
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type'       => 'required|in:tab_switch,window_blur,fullscreen_exit',
            'session_id' => 'required|exists:class_sessions,id',
        ]);

        $user    = Auth::user();
        $device  = $user->approvedDevice();
        /** @var ClassSession $session */
        $session = ClassSession::findOrNew($request->session_id);

        if (!$device || !$session || !$session->isActive()) {
            return response()->json(['ok' => false], 422);
        }

        // Increment violation count on the pivot
        $pivot = SessionDevice::where('session_id', $session->id)
                              ->where('device_id', $device->id)
                              ->first();

        if ($pivot) {
            $pivot->incrementViolation();
            $warningLevel = $pivot->warningLevel();
        } else {
            $warningLevel = 0;
        }

        // Log it
        ActivityLog::record(
            action: 'focus.violation',
            description: "Student switched away from session ({$request->type})",
            userId: $user->id,
            deviceId: $device?->id,
            sessionId: $session->id,
            metadata: [
                'violation_type'  => $request->type,
                'warning_level'   => $warningLevel,
                'violation_count' => $pivot?->violation_count,
            ]
        );

        return response()->json([
            'ok'            => true,
            'warning_level' => $warningLevel,
            'message'       => $this->warningMessage($warningLevel),
        ]);
    }

    // Called by student page to check if teacher locked their device
    public function lockStatus(Request $request, ClassSession $session): JsonResponse
    {
        $device = Auth::user()->approvedDevice();

        if (!$device) {
            return response()->json(['is_locked' => false]);
        }

        $pivot = SessionDevice::where('session_id', $session->id)
                              ->where('device_id', $device->id)
                              ->first();

        return response()->json([
            'is_locked' => $pivot?->is_locked ?? false,
        ]);
    }

    private function warningMessage(int $level): string
    {
        return match($level) {
            1 => 'Please stay on this page during the session.',
            2 => 'Second warning — your teacher has been notified.',
            default => 'Multiple violations recorded. Your teacher can see this.',
        };
    }
}