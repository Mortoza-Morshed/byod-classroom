<div class="space-y-6 pb-12">

    {{-- Section 1: Stats Grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        
        {{-- Total Users --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 h-full w-[3px] bg-blue-500"></div>
            <p class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Total Users</p>
            <p class="mt-2 text-3xl font-extrabold text-[#ededed] tracking-tight tabular-nums">
                {{ array_sum($usersByRole) }}
            </p>
            <p class="mt-1 text-xs text-[#a1a1a1]">
                {{ $usersByRole['teacher'] ?? 0 }} Teachers · {{ $usersByRole['student'] ?? 0 }} Students · {{ $usersByRole['admin'] ?? 0 }} Admins
            </p>
        </div>

        {{-- Total Devices --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 h-full w-[3px] bg-sky-500"></div>
            <p class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Total Devices</p>
            <p class="mt-2 text-3xl font-extrabold text-[#ededed] tracking-tight tabular-nums">
                {{ $devicesByStatus->sum() }}
            </p>
            <p class="mt-1 text-xs text-[#a1a1a1]">
                {{ $devicesByStatus['approved'] ?? 0 }} Approved · {{ $devicesByStatus['pending'] ?? 0 }} Pending
            </p>
        </div>

        {{-- Total Classrooms --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 h-full w-[3px] bg-purple-500"></div>
            <p class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Total Classrooms</p>
            <p class="mt-2 text-3xl font-extrabold text-[#ededed] tracking-tight tabular-nums">
                {{ $activeClassrooms }}
            </p>
            <p class="mt-1 text-xs text-[#a1a1a1]">
                Active classrooms managed
            </p>
        </div>

        {{-- Active Sessions --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 h-full w-[3px] bg-emerald-500"></div>
            <p class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Active Sessions</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl font-extrabold tracking-tight tabular-nums {{ $activeSessions > 0 ? 'text-emerald-400' : 'text-[#ededed]' }}">
                    {{ $activeSessions }}
                </p>
                @if($activeSessions > 0)
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                @endif
            </div>
            <p class="mt-1 text-xs text-[#a1a1a1]">
                Currently running in real-time
            </p>
        </div>

        {{-- Violations --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 h-full w-[3px] bg-red-500"></div>
            <p class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Violations (Last 24h)</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight tabular-nums {{ $violationsLast24h > 0 ? 'text-red-400' : 'text-[#ededed]' }}">
                {{ $violationsLast24h }}
            </p>
            <p class="mt-1 text-xs text-[#a1a1a1]">
                Focus lost detection events
            </p>
        </div>

        {{-- Pending Approvals --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 h-full w-[3px] bg-amber-500"></div>
            <p class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Pending Approvals</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight tabular-nums {{ ($devicesByStatus['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-[#ededed]' }}">
                {{ $devicesByStatus['pending'] ?? 0 }}
            </p>
            <p class="mt-1 text-xs text-[#a1a1a1]">
                +{{ $newDevicesLast24h }} new registrations in 24h
            </p>
        </div>

    </div>

    {{-- Section 2: Active Sessions Panel --}}
    @if($activeSessionsList->isNotEmpty())
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] shadow-2xl overflow-hidden">
            <div class="border-b border-[#2a2a2a] px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[#ededed] flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        Live Active Sessions
                    </h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-[#1a1a1a] text-[10px] font-bold uppercase tracking-wider text-[#666666] border-b border-[#2a2a2a]">
                            <th class="px-6 py-3">Classroom & Teacher</th>
                            <th class="px-6 py-3">Session Title</th>
                            <th class="px-6 py-3">Started</th>
                            <th class="px-6 py-3 text-center">Connected</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @foreach($activeSessionsList as $session)
                            <tr class="hover:bg-[#1a1a1a]/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#ededed]">{{ $session->classroom->name }}</div>
                                    <div class="text-xs text-[#a1a1a1]">{{ $session->classroom->teacher->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-[#a1a1a1]">
                                    {{ $session->title }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[#ededed] font-medium tabular-nums">{{ $session->started_at->format('g:i A') }}</div>
                                    <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">{{ round(abs(now()->diffInMinutes($session->started_at))) }} mins ago</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-400">
                                        {{ $session->devices_count }} devices
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(auth()->id() === $session->classroom->teacher_id)
                                        <a href="{{ route('teacher.sessions.live', $session) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 px-3 py-1.5 text-xs font-bold text-[#0a0a0a] transition-all cursor-pointer shadow-md">
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

    {{-- Section 3: Pending Devices & Recent Activity --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        {{-- Left: Pending device approvals --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-[#2a2a2a] px-6 py-4 bg-[#111111]">
                <h2 class="text-base font-bold text-[#ededed]">Pending Approvals</h2>
                <a href="{{ route('admin.devices.index') }}" class="text-xs font-bold uppercase tracking-wider text-[#a1a1a1] hover:text-[#ededed] transition-colors">View all</a>
            </div>
            
            <div class="divide-y divide-[#2a2a2a]">
                @forelse($pendingDevices as $device)
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-[#1a1a1a]/30 transition-colors">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-[#ededed]">{{ $device->name }}</p>
                            <p class="truncate text-xs text-[#a1a1a1] font-semibold">{{ $device->user->name }} · <span class="capitalize">{{ $device->device_type }}</span></p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button 
                                wire:click="approve({{ $device->id }})"
                                wire:loading.attr="disabled"
                                class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 text-xs font-bold text-emerald-400 hover:bg-emerald-500 hover:text-[#0a0a0a] transition-all cursor-pointer"
                            >
                                Approve
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
                        <div class="rounded-full bg-[#1a1a1a] p-3 border border-[#2a2a2a]">
                            <flux:icon.check-circle class="h-6 w-6 text-emerald-400" />
                        </div>
                        <p class="mt-3 text-sm font-bold text-[#ededed]">All caught up!</p>
                        <p class="mt-1 text-xs text-[#666666]">No pending device approvals.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Recent Activity --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-[#2a2a2a] px-6 py-4 bg-[#111111]">
                <h2 class="text-base font-bold text-[#ededed]">Recent Activity</h2>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-bold uppercase tracking-wider text-[#a1a1a1] hover:text-[#ededed] transition-colors">View logs</a>
            </div>
            
            <div class="divide-y divide-[#2a2a2a]">
                @forelse($recentLogs as $log)
                    @php
                        $actionPrefix = explode('.', $log->action)[0];
                        $dotColor = match($actionPrefix) {
                            'focus'    => 'bg-red-500',
                            'resource' => 'bg-emerald-500',
                            'device'   => 'bg-amber-500',
                            'user'     => 'bg-blue-500',
                            'session'  => 'bg-purple-500',
                            default    => 'bg-sky-500',
                        };
                        $actionColor = match($actionPrefix) {
                            'focus'    => 'text-red-400',
                            'resource' => 'text-emerald-400',
                            'device'   => 'text-amber-400',
                            'user'     => 'text-blue-400',
                            'session'  => 'text-purple-400',
                            default    => 'text-sky-400',
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-3 hover:bg-[#1a1a1a]/30 transition-colors">
                        <div class="relative mt-1.5 flex shrink-0 flex-col items-center">
                            <span class="h-2 w-2 rounded-full {{ $dotColor }} shadow-[0_0_8px_currentColor]"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-[#ededed] font-medium leading-relaxed">{{ $log->description }}</p>
                            <p class="mt-0.5 font-mono text-[10px] uppercase font-bold tracking-wider {{ $actionColor }}">{{ $log->action }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs text-[#666666] tabular-nums font-semibold">{{ $log->created_at->diffForHumans(short: true) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
                        <div class="rounded-full bg-[#1a1a1a] p-3 border border-[#2a2a2a]">
                            <flux:icon.clipboard-document-list class="h-6 w-6 text-[#666666]" />
                        </div>
                        <p class="mt-3 text-sm font-bold text-[#ededed]">No activity yet</p>
                        <p class="mt-1 text-xs text-[#666666]">System actions will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Section 4: 7-Day Activity Bars --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        {{-- Sessions --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-blue-500/25 to-transparent"></div>
            <h2 class="text-sm font-bold text-[#ededed] uppercase tracking-wider text-[10px] text-[#666666]">Sessions (Last 7 Days)</h2>
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
                        <span class="mb-2 text-xs font-bold tabular-nums transition-colors {{ $isZero ? 'text-[#666666]' : 'text-blue-400' }}">
                            {{ $item['count'] }}
                        </span>
                        <div class="w-full rounded-t-sm transition-all duration-300 group-hover:bg-blue-400 {{ $isZero ? 'bg-[#1a1a1a]' : 'bg-blue-500/80 shadow-[0_0_12px_rgba(59,130,246,0.3)]' }}" style="height: {{ $height }}%;"></div>
                        <span class="absolute -bottom-5 whitespace-nowrap text-[10px] font-bold text-[#666666] uppercase tracking-wider">
                            {{ \Carbon\Carbon::parse($item['date'])->format('D') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Violations --}}
        <div class="rounded-xl border border-[#2a2a2a] bg-[#111111] p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-red-500/25 to-transparent"></div>
            <h2 class="text-sm font-bold text-[#ededed] uppercase tracking-wider text-[10px] text-[#666666]">Violations (Last 7 Days)</h2>
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
                        <span class="mb-2 text-xs font-bold tabular-nums transition-colors {{ $isZero ? 'text-[#666666]' : 'text-red-400' }}">
                            {{ $item['count'] }}
                        </span>
                        <div class="w-full rounded-t-sm transition-all duration-300 group-hover:bg-red-400 {{ $isZero ? 'bg-[#1a1a1a]' : 'bg-red-500/80 shadow-[0_0_12px_rgba(239,68,68,0.3)]' }}" style="height: {{ $height }}%;"></div>
                        <span class="absolute -bottom-5 whitespace-nowrap text-[10px] font-bold text-[#666666] uppercase tracking-wider">
                            {{ \Carbon\Carbon::parse($item['date'])->format('D') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
