<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogViewer extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterAction = 'all';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAction(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterAction = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::query()
            ->with(['user.roles', 'device', 'session']);

        // Search filter
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('action', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function (Builder $uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Action group filter
        if ($this->filterAction !== 'all') {
            $query->where('action', 'like', $this->filterAction.'.%');
        }

        // Date filters
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->latest('created_at')->paginate(25);

        // Stats calculations
        $today = Carbon::today();

        $totalLogsToday = ActivityLog::query()->whereDate('created_at', $today)->count();
        $focusViolationsToday = ActivityLog::query()->whereDate('created_at', $today)->where('action', 'focus.violation')->count();
        $sessionsStartedToday = ActivityLog::query()->whereDate('created_at', $today)->where('action', 'session.started')->count();

        // Most active user today
        $mostActiveUserLog = ActivityLog::query()
            ->whereDate('created_at', $today)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->first();

        $mostActiveUser = null;
        if ($mostActiveUserLog) {
            $mostActiveUser = User::find($mostActiveUserLog->user_id);
        }

        return view('livewire.admin.activity-log-viewer', [
            'logs' => $logs,
            'totalLogsToday' => $totalLogsToday,
            'focusViolationsToday' => $focusViolationsToday,
            'sessionsStartedToday' => $sessionsStartedToday,
            'mostActiveUser' => $mostActiveUser,
        ]);
    }
}
