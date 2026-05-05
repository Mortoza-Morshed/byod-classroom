<div class="space-y-6 pb-12">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
            <flux:icon.arrow-left class="h-6 w-6" />
        </a>
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">User Profile</h1>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif
    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle">{{ session('error') }}</flux:callout>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: Basic Info --}}
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-zinc-100 text-3xl font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-zinc-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-zinc-500">{{ $user->email }}</p>
                    
                    <div class="mt-4 flex flex-wrap justify-center gap-1">
                        @foreach ($user->roles as $role)
                            <flux:badge color="{{ match($role->name) { 'admin' => 'indigo', 'teacher' => 'emerald', 'student' => 'sky', default => 'zinc' } }}">
                                {{ ucfirst($role->name) }}
                            </flux:badge>
                        @endforeach
                    </div>

                    <div class="mt-6 w-full space-y-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Status</span>
                            <flux:badge size="sm" color="{{ $user->is_active ? 'emerald' : 'red' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Member Since</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $user->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>

                    @if ($user->id !== auth()->id())
                        <div class="mt-8 w-full">
                            <flux:button 
                                wire:click="toggleActive" 
                                variant="{{ $user->is_active ? 'danger' : 'primary' }}"
                                class="w-full"
                            >
                                {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Devices --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Registered Devices</h3>
                <div class="mt-4 space-y-4">
                    @forelse ($user->devices as $device)
                        <div class="flex items-start gap-3 rounded-lg border border-zinc-100 p-3 dark:border-zinc-800">
                            <flux:icon.device-phone-mobile class="h-5 w-5 text-zinc-400" />
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $device->name }}</p>
                                <p class="text-xs text-zinc-500">ID: {{ $device->device_identifier }}</p>
                                <p class="mt-1 text-[10px] text-zinc-400">Last seen: {{ $device->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-4 text-sm text-zinc-400 italic">No devices registered.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Activity & Relationships --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Tabs / Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Classrooms</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $user->hasRole('teacher') ? $user->classroomsAsTeacher->count() : $user->classroomsAsStudent->count() }}
                    </p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Total Logs</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $activityLogs->total() }}</p>
                </div>
            </div>

            {{-- Recent Activity Log --}}
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Recent Activity</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse ($activityLogs as $log)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div @class([
                                                'h-2 w-2 rounded-full shrink-0',
                                                'bg-red-500' => str_contains($log->action, 'violation') || str_contains($log->action, 'deactivated'),
                                                'bg-emerald-500' => str_contains($log->action, 'activated') || str_contains($log->action, 'start'),
                                                'bg-sky-500' => !str_contains($log->action, 'violation') && !str_contains($log->action, 'activated'),
                                            ])></div>
                                            <div>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $log->description }}</p>
                                                <p class="text-xs text-zinc-500">
                                                    @if($log->session)
                                                        Session: {{ $log->session->title }} ({{ $log->session->classroom->name }})
                                                    @else
                                                        System Action
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-zinc-400 text-xs">
                                        {{ $log->created_at->format('M j, H:i') }}
                                        <div class="text-[10px]">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-12 text-center text-zinc-400 italic">No activity recorded for this user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($activityLogs->hasPages())
                    <div class="border-t border-zinc-100 px-6 py-3 dark:border-zinc-800">
                        {{ $activityLogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
