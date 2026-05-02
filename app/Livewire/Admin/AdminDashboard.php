<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\ClassSession;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function approve(int $deviceId)
    {
        $device = Device::findOrFail($deviceId);
        
        if ($device->status !== 'approved') {
            $device->update(['status' => 'approved']);
            
            ActivityLog::record(
                action: 'device.approved',
                description: "Approved device: {$device->name}",
                metadata: ['device_id' => $device->id]
            );
            
            $this->dispatch('notify', message: "Device {$device->name} approved.", type: 'success');
        }
    }

    public function render()
    {
        // Real-time stats
        $usersByRole = [
            'admin' => User::query()->role('admin')->count(),
            'teacher' => User::query()->role('teacher')->count(),
            'student' => User::query()->role('student')->count(),
        ];
            
        $devicesByStatus = Device::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
            
        $activeClassrooms = Classroom::where('is_active', true)->count();
        $activeSessions = ClassSession::where('status', 'active')->count();
        
        $violationsLast24h = ActivityLog::where('action', 'focus.violation')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
            
        $newDevicesLast24h = Device::where('created_at', '>=', now()->subHours(24))->count();

        // Activity sparkline data (last 7 days)
        $dates = collect(range(6, 0))->map(fn ($days) => now()->subDays($days)->format('Y-m-d'));
        
        $sessionsData = ClassSession::query()
            ->where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');
            
        $violationsData = ActivityLog::query()
            ->where('action', 'focus.violation')
            ->where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $sessionsChart = [];
        $violationsChart = [];
        
        foreach ($dates as $date) {
            $sessionsChart[] = ['date' => $date, 'count' => $sessionsData[$date] ?? 0];
            $violationsChart[] = ['date' => $date, 'count' => $violationsData[$date] ?? 0];
        }

        // Recent items
        $pendingDevices = Device::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
            
        $recentLogs = ActivityLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();
            
        $activeSessionsList = ClassSession::with(['classroom.teacher'])
            ->withCount('devices')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('livewire.admin.admin-dashboard', [
            'usersByRole' => $usersByRole,
            'devicesByStatus' => $devicesByStatus,
            'activeClassrooms' => $activeClassrooms,
            'activeSessions' => $activeSessions,
            'violationsLast24h' => $violationsLast24h,
            'newDevicesLast24h' => $newDevicesLast24h,
            'sessionsChart' => json_encode($sessionsChart),
            'violationsChart' => json_encode($violationsChart),
            'pendingDevices' => $pendingDevices,
            'recentLogs' => $recentLogs,
            'activeSessionsList' => $activeSessionsList,
        ]);
    }
}
