<div class="space-y-6 pb-10">

    {{-- ── 1. Stats Row ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Total Logs Today</p>
            <p class="mt-1 text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ number_format($totalLogsToday) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Focus Violations Today</p>
            <p class="mt-1 text-2xl font-bold {{ $focusViolationsToday > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-zinc-100' }}">
                {{ number_format($focusViolationsToday) }}
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Sessions Started Today</p>
            <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($sessionsStartedToday) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Most Active User Today</p>
            @if ($mostActiveUser)
                <p class="mt-1 truncate text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $mostActiveUser->name }}</p>
            @else
                <p class="mt-1 text-sm font-medium text-zinc-500">None</p>
            @endif
        </div>
    </div>

    {{-- ── 2. Filter Bar ───────────────────────────────────────────── --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            
            {{-- Search --}}
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search action, desc, or user…"
                class="flex-1 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
            />

            {{-- Action Filter --}}
            <select
                wire:model.live="filterAction"
                class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
            >
                <option value="all">All Actions</option>
                <option value="session">Session (session.*)</option>
                <option value="device">Device (device.*)</option>
                <option value="focus">Focus (focus.*)</option>
                <option value="classroom">Classroom (classroom.*)</option>
                <option value="policy">Policy (policy.*)</option>
                <option value="resource">Resource (resource.*)</option>
                <option value="user">User (user.*)</option>
            </select>

            {{-- Date Filters --}}
            <input
                wire:model.live="dateFrom"
                type="date"
                class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                title="Date From"
            />
            <span class="text-zinc-400 dark:text-zinc-500">—</span>
            <input
                wire:model.live="dateTo"
                type="date"
                class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                title="Date To"
            />

            {{-- Reset --}}
            <button
                wire:click="resetFilters"
                class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
            >
                Reset
            </button>
        </div>
    </div>

    {{-- ── 3. Data Table ───────────────────────────────────────────── --}}
    <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900/50 dark:text-zinc-400">
                    <th class="px-4 py-3">Timestamp</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Device</th>
                    <th class="px-4 py-3">Session</th>
                    <th class="px-4 py-3">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse ($logs as $log)
                    @php
                        $actionPrefix = explode('.', $log->action)[0];
                        $actionColor = match($actionPrefix) {
                            'focus'     => 'text-red-600 dark:text-red-400',
                            'session'   => 'text-blue-600 dark:text-blue-400',
                            'resource'  => 'text-emerald-600 dark:text-emerald-400',
                            'device'    => 'text-amber-600 dark:text-amber-400',
                            'classroom' => 'text-purple-600 dark:text-purple-400',
                            'policy'    => 'text-indigo-600 dark:text-indigo-400',
                            default     => 'text-zinc-600 dark:text-zinc-400',
                        };

                        // User badge config
                        if ($log->user) {
                            $role = $log->user->roles->first()?->name ?? 'student';
                            $roleBadge = match($role) {
                                'admin'   => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400',
                                'teacher' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
                                default   => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400',
                            };
                        }
                    @endphp
                    <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-700/30">
                        {{-- Timestamp --}}
                        <td class="whitespace-nowrap px-4 py-3 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>

                        {{-- Action --}}
                        <td class="px-4 py-3">
                            <span class="font-mono text-[11px] font-medium {{ $actionColor }}">
                                {{ $log->action }}
                            </span>
                        </td>

                        {{-- Description --}}
                        <td class="px-4 py-3 text-zinc-800 dark:text-zinc-200">
                            {{ $log->description ?: '—' }}
                        </td>

                        {{-- User --}}
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($log->user)
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $log->user->name }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider {{ $roleBadge }}">
                                        {{ $role }}
                                    </span>
                                </div>
                            @else
                                <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    System
                                </span>
                            @endif
                        </td>

                        {{-- Device --}}
                        <td class="px-4 py-3">
                            @if ($log->device)
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $log->device->name }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>

                        {{-- Session --}}
                        <td class="px-4 py-3">
                            @if ($log->session)
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $log->session->title }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>

                        {{-- IP Address --}}
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $log->ip_address ?? '—' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-zinc-400">
                            No audit logs found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($logs->hasPages())
            <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
