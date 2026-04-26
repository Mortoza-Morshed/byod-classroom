<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityLog;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeviceList extends Component
{
    public string $search = '';

    public string $filterStatus = 'all';

    public function approve(int $deviceId): void
    {
        $device = Device::findOrFail($deviceId);

        // Make sure this student is in one of the teacher's classrooms
        $this->authorizeDevice($device);

        $device->approve();

        ActivityLog::record(
            action: 'device.approved',
            description: "Teacher approved device: {$device->name}",
            deviceId: $device->id,
        );
    }

    public function block(int $deviceId): void
    {
        $device = Device::findOrFail($deviceId);
        $this->authorizeDevice($device);
        $device->block();

        ActivityLog::record(
            action: 'device.blocked',
            description: "Teacher blocked device: {$device->name}",
            deviceId: $device->id,
        );
    }

    private function authorizeDevice(Device $device): void
    {
        $teacherStudentIds = Auth::user()
            ->classrooms()
            ->with('students')
            ->get()
            ->flatMap(fn ($c) => $c->students->pluck('id'))
            ->unique();

        abort_unless($teacherStudentIds->contains($device->user_id), 403);
    }

    public function render()
    {
        // Only show devices belonging to students in this teacher's classrooms
        $studentIds = Auth::user()
            ->classrooms()
            ->with('students')
            ->get()
            ->flatMap(fn ($c) => $c->students->pluck('id'))
            ->unique();

        $devices = Device::with('user')
            ->whereIn('user_id', $studentIds)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus)
            )
            ->latest()
            ->get();

        return view('livewire.teacher.device-list', compact('devices'));
    }
}
