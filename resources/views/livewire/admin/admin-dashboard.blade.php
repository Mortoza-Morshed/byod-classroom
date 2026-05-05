<div class="space-y-6 pb-12">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Section 1: Stats Grid --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        
        {{-- Row 1 --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Users</p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ array_sum($usersByRole) }}
            </p>
            <p class="mt-1 text-xs text-zinc-400">
                {{ $usersByRole['teacher'] ?? 0 }} Teachers · {{ $usersByRole['student'] ?? 0 }} Students · {{ $usersByRole['admin'] ?? 0 }} Admins
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Devices</p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $devicesByStatus->sum() }}
            </p>
            <p class="mt-1 text-xs text-zinc-400">
                {{ $devicesByStatus['approved'] ?? 0 }} Approved · {{ $devicesByStatus['pending'] ?? 0 }} Pending
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Classrooms</p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $activeClassrooms }}
            </p>
            <p class="mt-1 text-xs text-zinc-400">
                Active classrooms
            </p>
        </div>

        {{-- Row 2 --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 relative overflow-hidden">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active Sessions</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl font-bold {{ $activeSessions > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                    {{ $activeSessions }}
                </p>
                @if($activeSessions > 0)
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                @endif
            </div>
            <p class="mt-1 text-xs text-zinc-400">
                Currently running
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Violations (Last 24h)</p>
            <p class="mt-2 text-3xl font-bold {{ $violationsLast24h > 0 ? 'text-amber-600 dark:text-amber-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ $violationsLast24h }}
            </p>
            <p class="mt-1 text-xs text-zinc-400">
                Focus lost events
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pending Approvals</p>
            <p class="mt-2 text-3xl font-bold {{ ($devicesByStatus['pending'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ $devicesByStatus['pending'] ?? 0 }}
            </p>
            <p class="mt-1 text-xs text-zinc-400">
                +{{ $newDevicesLast24h }} new registrations in 24h
            </p>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Section 2: Active Sessions Panel --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($activeSessionsList->isNotEmpty())
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        Live Sessions
                    </h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-zinc-50 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                            <th class="px-6 py-3">Classroom & Teacher</th>
                            <th class="px-6 py-3">Session Title</th>
                            <th class="px-6 py-3">Started</th>
                            <th class="px-6 py-3 text-center">Connected</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($activeSessionsList as $session)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $session->classroom->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $session->classroom->teacher->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-zinc-600 dark:text-zinc-300">
                                    {{ $session->title }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-zinc-600 dark:text-zinc-300">{{ $session->started_at->format('g:i A') }}</div>
                                    <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ round(abs(now()->diffInMinutes($session->started_at))) }} mins ago</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ $session->devices_count }} devices
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(auth()->id() === $session->classroom->teacher_id)
                                        <a href="{{ route('teacher.sessions.live', $session) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-emerald-700">
                                            <flux:icon.play class="h-3.5 w-3.5" />
                                            Go Live
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Section 3: Pending Devices & Recent Activity --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        {{-- Left: Pending device approvals --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Pending Approvals</h2>
                <a href="{{ route('admin.devices.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">View all</a>
            </div>
            
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($pendingDevices as $device)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $device->name }}</p>
                            <p class="truncate text-xs text-zinc-500">{{ $device->user->name }} · <span class="capitalize">{{ $device->device_type }}</span></p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button 
                                wire:click="approve({{ $device->id }})"
                                wire:loading.attr="disabled"
                                class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20"
                            >
                                Approve
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
                        <div class="rounded-full bg-zinc-100 p-3 dark:bg-zinc-800">
                            <flux:icon.check-circle class="h-6 w-6 text-zinc-400 dark:text-zinc-500" />
                        </div>
                        <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">All caught up!</p>
                        <p class="mt-1 text-xs text-zinc-500">No pending device approvals.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Recent Activity --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Recent Activity</h2>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">View logs</a>
            </div>
            
            <div class="divide-y divide-zinc-50 dark:divide-zinc-800">
                @forelse($recentLogs as $log)
                    @php
                        $actionPrefix = explode('.', $log->action)[0];
                        $dotColor = match($actionPrefix) {
                            'focus'    => 'bg-red-500',
                            'resource' => 'bg-emerald-500',
                            'device'   => 'bg-amber-500',
                            'user'     => 'bg-indigo-500',
                            'session'  => 'bg-purple-500',
                            default    => 'bg-sky-500',
                        };
                        $actionColor = match($actionPrefix) {
                            'focus'    => 'text-red-600 dark:text-red-400',
                            'resource' => 'text-emerald-600 dark:text-emerald-400',
                            'device'   => 'text-amber-600 dark:text-amber-400',
                            'user'     => 'text-indigo-600 dark:text-indigo-400',
                            'session'  => 'text-purple-600 dark:text-purple-400',
                            default    => 'text-sky-600 dark:text-sky-400',
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-3">
                        <div class="relative mt-1.5 flex shrink-0 flex-col items-center">
                            <span class="h-2 w-2 rounded-full {{ $dotColor }}"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-zinc-800 dark:text-zinc-200">{{ $log->description }}</p>
                            <p class="mt-0.5 font-mono text-[10px] {{ $actionColor }}">{{ $log->action }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs text-zinc-400">{{ $log->created_at->diffForHumans(short: true) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
                        <div class="rounded-full bg-zinc-100 p-3 dark:bg-zinc-800">
                            <flux:icon.clipboard-document-list class="h-6 w-6 text-zinc-400 dark:text-zinc-500" />
                        </div>
                        <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">No activity yet</p>
                        <p class="mt-1 text-xs text-zinc-500">System actions will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Section 4: 7-Day Activity Bars --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        {{-- Sessions --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Sessions (Last 7 Days)</h2>
            @php
                $sChart = json_decode($sessionsChart, true);
                $sMax = max(array_column($sChart, 'count') ?: [1]);
            @endphp
            <div class="mt-6 flex h-40 items-end gap-2 overflow-x-auto pb-6">
                @foreach ($sChart as $item)
                    @php
                        $height = max(($item['count'] / $sMax) * 100, 2);
                        $isZero = $item['count'] === 0;
                    @endphp
                    <div class="group relative flex flex-1 flex-col items-center justify-end" style="min-width: 2.5rem;">
                        <span class="mb-2 text-xs font-medium {{ $isZero ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-600 dark:text-zinc-300' }}">
                            {{ $item['count'] }}
                        </span>
                        <div class="w-full rounded-t-sm transition-all duration-300 group-hover:bg-indigo-500 {{ $isZero ? 'bg-zinc-100 dark:bg-zinc-800' : 'bg-indigo-600 dark:bg-indigo-500' }}" style="height: {{ $height }}%;"></div>
                        <span class="absolute -bottom-5 whitespace-nowrap text-[10px] text-zinc-400">
                            {{ \Carbon\Carbon::parse($item['date'])->format('D') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Violations --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Violations (Last 7 Days)</h2>
            @php
                $vChart = json_decode($violationsChart, true);
                $vMax = max(array_column($vChart, 'count') ?: [1]);
            @endphp
            <div class="mt-6 flex h-40 items-end gap-2 overflow-x-auto pb-6">
                @foreach ($vChart as $item)
                    @php
                        $height = max(($item['count'] / $vMax) * 100, 2);
                        $isZero = $item['count'] === 0;
                    @endphp
                    <div class="group relative flex flex-1 flex-col items-center justify-end" style="min-width: 2.5rem;">
                        <span class="mb-2 text-xs font-medium {{ $isZero ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-600 dark:text-zinc-300' }}">
                            {{ $item['count'] }}
                        </span>
                        <div class="w-full rounded-t-sm transition-all duration-300 group-hover:bg-amber-500 {{ $isZero ? 'bg-zinc-100 dark:bg-zinc-800' : 'bg-amber-500 dark:bg-amber-600' }}" style="height: {{ $height }}%;"></div>
                        <span class="absolute -bottom-5 whitespace-nowrap text-[10px] text-zinc-400">
                            {{ \Carbon\Carbon::parse($item['date'])->format('D') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
