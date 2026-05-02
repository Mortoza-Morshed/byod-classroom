<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterRole = 'all';

    public string $filterStatus = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $userId): void
    {
        if (Auth::id() === $userId) {
            session()->flash('error', 'You cannot deactivate your own account.');

            return;
        }

        /** @var User $user */
        $user = User::findOrFail($userId);
        $isNowActive = !$user->is_active;

        $user->update(['is_active' => $isNowActive]);

        ActivityLog::record(
            action: $isNowActive ? 'user.activated' : 'user.deactivated',
            description: ($isNowActive ? 'Activated' : 'Deactivated') . " user: {$user->name}",
            userId: Auth::id(),
        );

        session()->flash(
            'success',
            "{$user->name} has been " . ($isNowActive ? 'activated' : 'deactivated') . '.'
        );
    }

    public function render()
    {
        $users = User::query()
            ->with(['roles', 'devices'])
            ->when(
                $this->search,
                fn($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when($this->filterRole !== 'all', fn($q) => $q->role($this->filterRole))
            ->when($this->filterStatus === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15);

        $roleCounts = [
            'all' => User::query()->count(),
            'admin' => User::query()->role('admin')->count(),
            'teacher' => User::query()->role('teacher')->count(),
            'student' => User::query()->role('student')->count(),
        ];

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'roleCounts' => $roleCounts,
        ]);
    }
}
