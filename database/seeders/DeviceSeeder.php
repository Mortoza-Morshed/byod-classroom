<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::role('student')->get();

        $deviceTypes = ['laptop', 'tablet', 'phone'];

        $deviceNames = [
            'laptop' => ["'s HP Laptop", "'s Dell Laptop", "'s Lenovo ThinkPad", "'s Acer Aspire"],
            'tablet' => ["'s iPad", "'s Samsung Tab", "'s Lenovo Tab"],
            'phone'  => ["'s iPhone", "'s Samsung Galaxy", "'s OnePlus", "'s Redmi"],
        ];

        foreach ($students as $student) {
            $type = $deviceTypes[array_rand($deviceTypes)];
            $suffix = $deviceNames[$type][array_rand($deviceNames[$type])];
            $firstName = explode(' ', $student->name)[0];

            Device::create([
                'user_id'       => $student->id,
                'name'          => $firstName . $suffix,
                'mac_address'   => $this->randomMac(),
                'device_type'   => $type,
                'status'        => 'approved', // pre-approved for dev
                'registered_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Give teachers a device too (so they can join sessions for testing)
        $teachers = User::role('teacher')->get();
        foreach ($teachers as $teacher) {
            $firstName = explode(' ', $teacher->name)[0];
            Device::create([
                'user_id'       => $teacher->id,
                'name'          => $firstName . "'s Teaching Laptop",
                'mac_address'   => $this->randomMac(),
                'device_type'   => 'laptop',
                'status'        => 'approved',
                'registered_at' => now()->subDays(rand(30, 60)),
            ]);
        }

        $this->command->info('Devices created: 1 per student + 1 per teacher');
    }

    private function randomMac(): string
    {
        return implode(':', array_map(
            fn() => strtoupper(dechex(rand(0, 255))),
            range(1, 6)
        ));
    }
}