<div class="space-y-6 pb-12">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. Report Header --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="mb-1 flex items-center gap-2">
                    <flux:badge
                        color="{{ $session->isActive() ? 'green' : 'zinc' }}"
                        size="sm"
                    >{{ ucfirst($session->status) }}</flux:badge>
                </div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $session->title }}</h1>
                <p class="mt-0.5 text-sm text-zinc-500">
                    {{ $session->classroom->name }}
                    @if ($session->classroom->subject)
                        &mdash; {{ $session->classroom->subject }}
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('teacher.sessions.report.pdf', $session) }}"
                    target="_blank"
                    class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
                >
                    <flux:icon.arrow-down-tray class="h-4 w-4" />
                    Download PDF
                </a>
                
                <flux:button
                    href="{{ route('teacher.classrooms.show', $session->classroom_id) }}"
                    variant="ghost"
                    size="sm"
                    icon="arrow-left"
                >Back to Classroom</flux:button>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-x-8 gap-y-2 border-t border-zinc-100 pt-4 text-sm dark:border-zinc-800 sm:grid-cols-3">
            <div>
                <p class="text-xs text-zinc-400">Started</p>
                <p class="font-medium text-zinc-700 dark:text-zinc-300">
                    {{ $session->started_at?->format('d M Y, g:i A') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-zinc-400">Ended</p>
                <p class="font-medium text-zinc-700 dark:text-zinc-300">
                    {{ $session->ended_at?->format('d M Y, g:i A') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-zinc-400">Duration</p>
                <p class="font-medium text-zinc-700 dark:text-zinc-300">{{ $duration ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. Summary Stats --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Students Connected</p>
            <p class="mt-1 text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $totalStudents }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Total Violations</p>
            <p class="mt-1 text-3xl font-bold {{ $totalViolations > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $totalViolations }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Resources Shared</p>
            <p class="mt-1 text-3xl font-bold text-sky-600 dark:text-sky-400">{{ $resourcesShared }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Duration</p>
            <p class="mt-1 text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $duration ?? '—' }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs text-zinc-400">Most Violations</p>
            @if ($worstOffender && $worstOffender->violation_count > 0)
                <p class="mt-1 text-sm font-bold text-red-600 dark:text-red-400">
                    {{ $worstOffender->device->user->name ?? '—' }}
                </p>
                <p class="text-xs text-zinc-400">{{ $worstOffender->violation_count }} violations</p>
            @else
                <p class="mt-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">None</p>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. Student Breakdown Table --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
            <h2 class="text-base font-semibold text-zinc-800 dark:text-zinc-200">Student Breakdown</h2>
            <p class="text-xs text-zinc-400">Sorted by violation count descending</p>
        </div>

        @if ($sessionDevices->isEmpty())
            <p class="py-10 text-center text-sm text-zinc-400">No students connected to this session.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 text-left text-xs font-medium uppercase tracking-wide text-zinc-400 dark:border-zinc-800">
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Device</th>
                            <th class="px-6 py-3">Joined</th>
                            <th class="px-6 py-3">Time in Session</th>
                            <th class="px-6 py-3">Violations</th>
                            <th class="px-6 py-3">Warning Level</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        @foreach ($sessionDevices as $sd)
                            @php
                                $student = $sd->device->user;
                                $violations = $sd->violation_count;
                                $warnLevel = $sd->warningLevel();

                                $violColor = match(true) {
                                    $violations >= 2 => 'text-red-600 dark:text-red-400 font-bold',
                                    $violations === 1 => 'text-amber-600 dark:text-amber-400 font-semibold',
                                    default => 'text-emerald-600 dark:text-emerald-400',
                                };

                                $warnLabel = match($warnLevel) {
                                    3 => ['Locked', 'text-red-600 dark:text-red-400'],
                                    2 => ['Second Warning', 'text-orange-500 dark:text-orange-400'],
                                    1 => ['Warning', 'text-amber-500 dark:text-amber-400'],
                                    default => ['Good', 'text-emerald-600 dark:text-emerald-400'],
                                };

                                $inSession = $sd->joined_at && $sd->left_at
                                    ? $sd->joined_at->diffForHumans($sd->left_at, true)
                                    : ($sd->joined_at ? $sd->joined_at->diffForHumans(now(), true) : '—');
                            @endphp
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40" wire:key="sd-{{ $sd->id }}">
                                <td class="px-6 py-3">
                                    <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $student->name ?? '—' }}</p>
                                    <p class="text-xs text-zinc-400">{{ $student->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    <p class="text-zinc-700 dark:text-zinc-300">{{ $sd->device->name }}</p>
                                    <p class="text-xs capitalize text-zinc-400">{{ $sd->device->device_type }}</p>
                                </td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $sd->joined_at?->format('g:i A') ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $inSession }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-lg {{ $violColor }}">{{ $violations }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-sm {{ $warnLabel[1] }}">{{ $warnLabel[0] }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    @if ($sd->left_at)
                                        <flux:badge size="sm" color="zinc">Left</flux:badge>
                                    @elseif ($sd->is_locked)
                                        <flux:badge size="sm" color="red">Locked</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="green">Active</flux:badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Bottom two-column layout --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- ── 4. Violation Breakdown ──────────────────────────────── --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-base font-semibold text-zinc-800 dark:text-zinc-200">Violation Breakdown</h2>

            @if ($totalViolations === 0)
                <p class="py-6 text-center text-sm text-zinc-400">No violations recorded.</p>
            @else
                <div class="space-y-4">
                    @php
                        $violationLabels = [
                            'tab_switch'      => 'Tab Switch',
                            'window_blur'     => 'Window Blur',
                            'fullscreen_exit' => 'Fullscreen Exit',
                        ];
                        $violationColors = [
                            'tab_switch'      => 'bg-red-500',
                            'window_blur'     => 'bg-orange-500',
                            'fullscreen_exit' => 'bg-amber-500',
                        ];
                    @endphp

                    @foreach ($violationBreakdown as $type => $count)
                        @php
                            $pct = $maxViolationTypeCount > 0
                                ? round(($count / $maxViolationTypeCount) * 100)
                                : 0;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $violationLabels[$type] }}
                                </span>
                                <span class="font-mono text-zinc-500">{{ $count }}</span>
                            </div>
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div
                                    class="h-2.5 rounded-full {{ $violationColors[$type] }} transition-all"
                                    style="width: {{ $pct }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── 5. Shared Resources ─────────────────────────────────── --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-base font-semibold text-zinc-800 dark:text-zinc-200">Shared Resources</h2>

            @if ($session->resources->isEmpty())
                <p class="py-6 text-center text-sm text-zinc-400">No resources were shared.</p>
            @else
                <div class="space-y-3">
                    @foreach ($session->resources as $resource)
                        <div class="flex items-center justify-between rounded-lg border border-zinc-100 px-4 py-3 dark:border-zinc-800">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $resource->title }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    Shared {{ $resource->created_at->format('g:i A') }}
                                </p>
                            </div>
                            <div class="ml-3 flex shrink-0 items-center gap-2">
                                <flux:badge size="sm" color="zinc">{{ $resource->type }}</flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="{{ $resource->rendering_mode === 'external' ? 'amber' : 'sky' }}"
                                >{{ $resource->rendering_mode }}</flux:badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 6. Full Activity Timeline --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
            <h2 class="text-base font-semibold text-zinc-800 dark:text-zinc-200">Activity Timeline</h2>
            <p class="text-xs text-zinc-400">Chronological log of all session events</p>
        </div>

        @if ($session->activityLogs->isEmpty())
            <p class="py-10 text-center text-sm text-zinc-400">No activity logged.</p>
        @else
            <div class="divide-y divide-zinc-50 dark:divide-zinc-800">
                @foreach ($session->activityLogs as $log)
                    @php
                        $actionPrefix = explode('.', $log->action)[0];
                        $dotColor = match($actionPrefix) {
                            'focus'    => 'bg-red-500',
                            'resource' => 'bg-emerald-500',
                            'device'   => 'bg-amber-500',
                            default    => 'bg-sky-500',
                        };
                        $actionColor = match($actionPrefix) {
                            'focus'    => 'text-red-600 dark:text-red-400',
                            'resource' => 'text-emerald-600 dark:text-emerald-400',
                            'device'   => 'text-amber-600 dark:text-amber-400',
                            default    => 'text-sky-600 dark:text-sky-400',
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-3" wire:key="log-{{ $log->id }}">
                        {{-- Timeline dot --}}
                        <div class="relative mt-1.5 flex shrink-0 flex-col items-center">
                            <span class="h-2 w-2 rounded-full {{ $dotColor }}"></span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-zinc-800 dark:text-zinc-200">{{ $log->description }}</p>
                            <p class="mt-0.5 font-mono text-[10px] {{ $actionColor }}">{{ $log->action }}</p>
                        </div>

                        <div class="shrink-0 text-right">
                            <p class="text-xs text-zinc-400">{{ $log->created_at->format('g:i:s A') }}</p>
                            @if ($log->user)
                                <p class="text-xs text-zinc-300 dark:text-zinc-600">{{ $log->user->name }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
