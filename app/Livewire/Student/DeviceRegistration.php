<?php

namespace App\Livewire\Student;

use App\Models\ActivityLog;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeviceRegistration extends Component
{
    public string $name = '';

    public string $mac_address = '';

    public ?Device $existingDevice = null;

    public function mount(): void
    {
        // Load the student's existing device if any
        $this->existingDevice = Auth::user()->devices()->latest()->first();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:60',
            'mac_address' => 'nullable|string|max:17',
        ];
    }

    public function register(): void
    {
        $this->validate();

        // Don't allow registering a second device if one is pending or approved
        if ($this->existingDevice &&
            in_array($this->existingDevice->status, ['pending', 'approved'])) {
            $this->addError('name', 'You already have a registered device.');

            return;
        }

        $device = Device::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'device_type' => 'laptop',
            'mac_address' => $this->mac_address ?: null,
            'status' => 'pending',
            'registered_at' => now(),
        ]);

        ActivityLog::record(
            action: 'device.registered',
            description: "Student registered device: {$device->name}",
            deviceId: $device->id,
        );

        $this->existingDevice = $device;
        $this->reset(['name', 'mac_address']);

        session()->flash('success', 'Device registered! Waiting for approval.');
    }

    public function render()
    {
        return view('livewire.student.device-registration');
    }
}
