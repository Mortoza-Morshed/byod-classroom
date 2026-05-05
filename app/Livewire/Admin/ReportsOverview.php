<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsOverview extends Component
{
    use WithPagination;

    public string $dateRange = '7';

    public string $selectedClassroom = 'all';

    public function updatingDateRange(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedClassroom(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $startDate = match ($this->dateRange) {
            '7' => now()->subDays(7)->startOfDay(),
            '30' => now()->subDays(30)->startOfDay(),
            '90' => now()->subDays(90)->startOfDay(),
            default => null,
        };

        // Base query for sessions
        $sessionsQuery = ClassSession::query()
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($this->selectedClassroom !== 'all', fn ($q) => $q->where('classroom_id', $this->selectedClassroom));

        $endedSessionsQuery = clone $sessionsQuery;
        $endedSessionsQuery->where('status', 'ended');

        $sessionIds = $sessionsQuery->pluck('id')->toArray();

        // 1. Total sessions run
        $totalSessions = $sessionsQuery->count();

        // 2. Total active session time (minutes)
        $endedSessions = $endedSessionsQuery->get(['started_at', 'ended_at']);
        $totalActiveTimeMinutes = $endedSessions->reduce(function ($carry, $session) {
            if ($session->started_at && $session->ended_at) {
                return $carry + round($session->started_at->diffInMinutes($session->ended_at));
            }

            return $carry;
        }, 0);

        // 3. Total students who attended
        $totalStudents = 0;
        if (! empty($sessionIds)) {
            $totalStudents = DB::table('session_devices')
                ->join('devices', 'session_devices.device_id', '=', 'devices.id')
                ->whereIn('session_devices.session_id', $sessionIds)
                ->distinct('devices.user_id')
                ->count('devices.user_id');
        }

        // 4. Total focus violations
        $totalViolations = 0;
        if (! empty($sessionIds)) {
            $totalViolations = DB::table('session_devices')
                ->whereIn('session_id', $sessionIds)
                ->sum('violation_count');
        }

        // 5. Most used classroom
        $mostUsedClassroom = null;
        $mostUsedClassroomId = ClassSession::query()
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->select('classroom_id', DB::raw('count(*) as total'))
            ->groupBy('classroom_id')
            ->orderByDesc('total')
            ->first()?->classroom_id;

        if ($mostUsedClassroomId) {
            $mostUsedClassroom = Classroom::find($mostUsedClassroomId);
        }

        // 6. Average violations per session
        $avgViolations = $totalSessions > 0 ? round($totalViolations / $totalSessions, 1) : 0;

        // -- Recent Sessions Table --
        $recentSessions = $endedSessionsQuery
            ->with(['classroom.teacher'])
            ->withCount('devices')
            ->latest('created_at')
            ->paginate(15);

        // -- Violations Breakdown Chart Data --
        $chartData = [];
        if ($startDate) {
            $days = match ($this->dateRange) {
                '7' => 7,
                '30' => 30,
                '90' => 90,
                default => 7,
            };

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $chartData[$date] = 0;
            }
        }

        $violationsQuery = ActivityLog::query()
            ->where('action', 'focus.violation')
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($this->selectedClassroom !== 'all', function ($q) use ($sessionIds) {
                $q->whereIn('session_id', $sessionIds);
            });

        $violationsByDate = $violationsQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        foreach ($violationsByDate as $row) {
            $date = Carbon::parse($row->date)->format('Y-m-d');
            if (isset($chartData[$date])) {
                $chartData[$date] = $row->count;
            } elseif ($this->dateRange === 'all') {
                $chartData[$date] = $row->count;
            }
        }

        if (empty($chartData)) {
            $chartData[now()->format('Y-m-d')] = 0;
        }

        $chartJson = json_encode(array_map(function ($date, $count) {
            return ['date' => $date, 'count' => $count];
        }, array_keys($chartData), array_values($chartData)));

        // -- Top Offenders Table --
        $topOffenders = collect();
        if (! empty($sessionIds)) {
            $topOffenders = DB::table('session_devices')
                ->join('devices', 'session_devices.device_id', '=', 'devices.id')
                ->join('users', 'devices.user_id', '=', 'users.id')
                ->whereIn('session_devices.session_id', $sessionIds)
                ->where('session_devices.violation_count', '>', 0)
                ->selectRaw('users.name, users.email, SUM(session_devices.violation_count) as total_violations, COUNT(DISTINCT session_devices.session_id) as sessions_attended')
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderByDesc('total_violations')
                ->limit(10)
                ->get();
        }

        // -- Classroom Comparison Table --
        $classroomStats = Classroom::query()
            ->when($this->selectedClassroom !== 'all', fn ($q) => $q->where('id', $this->selectedClassroom))
            ->get()
            ->map(function ($classroom) use ($startDate) {
                $classroomSessions = $classroom->sessions()
                    ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
                    ->pluck('id')->toArray();

                $totalVio = 0;
                $uniqueStudents = 0;
                $lastSession = null;

                if (! empty($classroomSessions)) {
                    $totalVio = DB::table('session_devices')
                        ->whereIn('session_id', $classroomSessions)
                        ->sum('violation_count');

                    $uniqueStudents = DB::table('session_devices')
                        ->join('devices', 'session_devices.device_id', '=', 'devices.id')
                        ->whereIn('session_devices.session_id', $classroomSessions)
                        ->distinct('devices.user_id')
                        ->count('devices.user_id');

                    $lastSession = DB::table('class_sessions')
                        ->whereIn('id', $classroomSessions)
                        ->latest('created_at')
                        ->value('created_at');
                }

                $avg = count($classroomSessions) > 0 ? round($totalVio / count($classroomSessions), 1) : 0;

                return (object) [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                    'sessions_count' => count($classroomSessions),
                    'total_violations' => $totalVio,
                    'avg_violations' => $avg,
                    'total_students' => $uniqueStudents,
                    'last_session_date' => $lastSession ? Carbon::parse($lastSession) : null,
                ];
            })
            ->filter(fn ($stat) => $stat->sessions_count > 0 || $this->selectedClassroom !== 'all')
            ->sortByDesc('sessions_count')
            ->values();

        $allClassrooms = Classroom::orderBy('name')->get();

        return view('livewire.admin.reports-overview', [
            'totalSessions' => $totalSessions,
            'totalActiveTimeMinutes' => $totalActiveTimeMinutes,
            'totalStudents' => $totalStudents,
            'totalViolations' => $totalViolations,
            'mostUsedClassroom' => $mostUsedClassroom,
            'avgViolations' => $avgViolations,
            'recentSessions' => $recentSessions,
            'chartJson' => $chartJson,
            'topOffenders' => $topOffenders,
            'classroomStats' => $classroomStats,
            'allClassrooms' => $allClassrooms,
        ]);
    }
}
