<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserShow extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load(['roles', 'devices', 'classroomsAsTeacher', 'classroomsAsStudent']);
    }

    public function toggleActive(): void
    {
        if (Auth::id() === $this->user->id) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        $isNowActive = !$this->user->is_active;

        $this->user->update(['is_active' => $isNowActive]);

        ActivityLog::record(
            action: $isNowActive ? 'user.activated' : 'user.deactivated',
            description: ($isNowActive ? 'Activated' : 'Deactivated') . " user: {$this->user->name}",
            userId: Auth::id(),
        );

        session()->flash(
            'success',
            "{$this->user->name} has been " . ($isNowActive ? 'activated' : 'deactivated') . '.'
        );
    }

    public function render()
    {
        $activityLogs = ActivityLog::query()
            ->where('user_id', $this->user->id)
            ->with(['session.classroom'])
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.admin.user-show', [
            'activityLogs' => $activityLogs,
        ]);
    }
}
