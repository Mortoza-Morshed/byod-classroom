<div class="space-y-8 pb-12">
    
    {{-- ── Header & Filters ────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 rounded-lg border border-zinc-200 bg-white p-1 dark:border-zinc-700 dark:bg-zinc-800">
            @php
                $ranges = [
                    '7' => '7 Days',
                    '30' => '30 Days',
                    '90' => '90 Days',
                    'all' => 'All Time',
                ];
            @endphp
            @foreach ($ranges as $val => $label)
                <button
                    wire:click="$set('dateRange', '{{ $val }}')"
                    @class([
                        'rounded-md px-4 py-1.5 text-sm font-medium transition',
                        'bg-indigo-600 text-white shadow-sm' => $dateRange === $val,
                        'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-700/50' => $dateRange !== $val,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <select
            wire:model.live="selectedClassroom"
            class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
        >
            <option value="all">All Classrooms</option>
            @foreach ($allClassrooms as $cr)
                <option value="{{ $cr->id }}">{{ $cr->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── 1. Summary Cards ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Sessions Run</p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalSessions) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active Session Time</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ $totalActiveTimeMinutes < 60 ? $totalActiveTimeMinutes . 'm' : floor($totalActiveTimeMinutes/60) . 'h ' . ($totalActiveTimeMinutes%60) . 'm' }}
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Students Engaged</p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalStudents) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Focus Violations</p>
            <p class="mt-2 text-3xl font-bold {{ $totalViolations > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ number_format($totalViolations) }}
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Average Violations/Session</p>
            <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $avgViolations }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Most Used Classroom</p>
            <p class="mt-2 truncate text-xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $mostUsedClassroom ? $mostUsedClassroom->name : '—' }}
            </p>
        </div>
    </div>

    {{-- ── 2. Violations Over Time (Tailwind Bar Chart) ────────────── --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Focus Violations Over Time</h2>
        
        @php
            $chartArr = json_decode($chartJson, true);
            $maxCount = max(array_column($chartArr, 'count') ?: [1]); // prevent div by zero
        @endphp

        <div class="mt-8 flex h-64 items-end gap-2 overflow-x-auto pb-6">
            @forelse ($chartArr as $item)
                @php
                    $heightPercent = ($item['count'] / $maxCount) * 100;
                    $isZero = $item['count'] === 0;
                @endphp
                <div class="group relative flex flex-1 flex-col items-center justify-end" style="min-width: 2.5rem;">
                    {{-- Tooltip / Label above bar --}}
                    <span class="mb-2 text-xs font-medium {{ $isZero ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-600 dark:text-zinc-300' }}">
                        {{ $item['count'] }}
                    </span>
                    
                    {{-- Bar --}}
                    <div 
                        class="w-full rounded-t-sm transition-all duration-300 group-hover:bg-indigo-500 {{ $isZero ? 'bg-zinc-100 dark:bg-zinc-800' : 'bg-indigo-600 dark:bg-indigo-500' }}"
                        style="height: {{ max($heightPercent, 2) }}%;"
                    ></div>
                    
                    {{-- Date label --}}
                    <span class="absolute -bottom-6 whitespace-nowrap text-[10px] text-zinc-400">
                        {{ \Carbon\Carbon::parse($item['date'])->format('M d') }}
                    </span>
                </div>
            @empty
                <div class="flex h-full w-full items-center justify-center text-sm text-zinc-400">
                    No data available for this period.
                </div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">
        {{-- ── 4. Top Offenders Table ────────────────────────────────── --}}
        @if ($topOffenders->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Top Offenders</h2>
                    <p class="text-sm text-zinc-500">Students with the most violations across sessions.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-zinc-50 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <th class="px-6 py-3">Student</th>
                                <th class="px-6 py-3 text-center">Sessions</th>
                                <th class="px-6 py-3 text-right">Violations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($topOffenders as $offender)
                                <tr class="{{ $offender->total_violations >= 5 ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $offender->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $offender->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-zinc-600 dark:text-zinc-300">
                                        {{ $offender->sessions_attended }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold {{ $offender->total_violations >= 5 ? 'text-amber-600 dark:text-amber-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                                        {{ $offender->total_violations }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── 5. Classroom Comparison Table ─────────────────────────── --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Classroom Comparison</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-zinc-50 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                            <th class="px-6 py-3">Classroom</th>
                            <th class="px-6 py-3 text-center">Sessions</th>
                            <th class="px-6 py-3 text-center">Avg Violations</th>
                            <th class="px-6 py-3 text-right">Last Session</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($classroomStats as $stat)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $stat->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $stat->total_students }} unique students</div>
                                </td>
                                <td class="px-6 py-4 text-center text-zinc-600 dark:text-zinc-300">
                                    {{ $stat->sessions_count }}
                                </td>
                                <td class="px-6 py-4 text-center text-zinc-600 dark:text-zinc-300">
                                    {{ $stat->avg_violations }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $stat->last_session_date ? $stat->last_session_date->format('M d, Y') : 'Never' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-zinc-500">No classroom data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── 3. Recent Sessions Table ────────────────────────────────── --}}
    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Sessions</h2>
            <p class="text-sm text-zinc-500">All ended sessions in the selected period.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-zinc-50 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        <th class="px-6 py-3">Session & Classroom</th>
                        <th class="px-6 py-3">Teacher</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Duration</th>
                        <th class="px-6 py-3 text-center">Students</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($recentSessions as $session)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $session->title }}</div>
                                <div class="text-xs text-zinc-500">{{ $session->classroom->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-300">
                                {{ $session->classroom->teacher->name }}
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-300">
                                {{ $session->started_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-300">
                                {{ $session->duration() ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center text-zinc-600 dark:text-zinc-300">
                                {{ $session->devices_count }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a 
                                    href="{{ route('teacher.sessions.report', $session) }}"
                                    class="inline-flex items-center rounded-lg border border-zinc-200 px-3 py-1.5 text-xs font-medium text-zinc-600 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700"
                                >
                                    View Report
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-zinc-500">No completed sessions found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($recentSessions->hasPages())
            <div class="border-t border-zinc-100 px-6 py-3 dark:border-zinc-800">
                {{ $recentSessions->links() }}
            </div>
        @endif
    </div>

</div>
