<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Device;
use Livewire\Component;
use Livewire\WithPagination;

class DeviceManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function approve(int $deviceId): void
    {
        $device = Device::findOrFail($deviceId);
        $device->approve();

        ActivityLog::record(
            action: 'device.approved',
            description: "Device approved: {$device->name} (owned by {$device->user->name})",
            deviceId: $device->id,
        );

        session()->flash('success', "Device '{$device->name}' approved.");
    }

    public function block(int $deviceId): void
    {
        $device = Device::findOrFail($deviceId);
        $device->block();

        ActivityLog::record(
            action: 'device.blocked',
            description: "Device blocked: {$device->name} (owned by {$device->user->name})",
            deviceId: $device->id,
        );

        session()->flash('success', "Device '{$device->name}' blocked.");
    }

    public function render()
    {
        $devices = Device::with('user')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus)
            )
            ->latest()
            ->paginate(10);

        return view('livewire.admin.device-manager', compact('devices'));
    }
}
