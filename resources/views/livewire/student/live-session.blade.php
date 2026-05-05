<div id="session-workspace" wire:poll.3s="checkSession" class="relative flex flex-col space-y-0 -mt-2 -mb-6" x-data x-on:session-ended.window="window.location.href='{{ route('student.sessions.summary', $classSession) }}'">
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Locked Device Overlay --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if ($isLocked)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/95 backdrop-blur-sm">
            <div class="mx-4 max-w-sm space-y-4 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-900/40 ring-4 ring-red-700/40">
                    <flux:icon.lock-closed class="h-10 w-10 text-red-400" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Device Locked</h2>
                    @if ($sessionDevice?->locked_until)
                        <p class="mt-2 text-zinc-400">
                            Automatically locked due to repeated focus violations.
                        </p>
                        <p
                            x-data="{
                                remaining: 10,
                                init() {
                                    const unlockAt = new Date(this.$el.dataset.unlockAt);
                                    const tick = () => {
                                        this.remaining = Math.max(0, Math.ceil((unlockAt - Date.now()) / 1000));
                                    };
                                    tick();
                                    setInterval(tick, 500);
                                }
                            }"
                            x-text="remaining"
                            data-unlock-at="{{ $sessionDevice->locked_until?->toISOString() }}"
                            class="mt-3 font-mono text-5xl font-bold text-red-400"
                        >10</p>
                        <p class="mt-1 text-sm text-zinc-500">seconds remaining</p>
                    @else
                        <p class="mt-2 text-zinc-400">Your teacher has paused your device. Please pay attention.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Top Bar --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="shrink-0 sticky top-0 z-30 border-b border-zinc-200 bg-white/95 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-950/95">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                {{-- Pulsing green LIVE badge --}}
                <span class="relative flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    LIVE
                </span>

                <div>
                    <h1 class="text-base font-bold text-zinc-900 dark:text-zinc-100">{{ $sessionData->title }}</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $sessionData->classroom->name }}
                        &mdash; {{ $sessionData->classroom->teacher->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- JS-driven session timer --}}
                <div class="text-right" wire:ignore>
                    <p
                        id="student-session-timer"
                        data-started-at="{{ $classSession->started_at?->toISOString() }}"
                        class="font-mono text-lg font-bold text-sky-600 dark:text-sky-400"
                    >00:00:00</p>
                    <p class="text-xs text-zinc-400">Duration</p>
                </div>

                {{-- Focus status — controlled by Alpine --}}
                <div
                    x-data="{ violated: false, timer: null }"
                    x-on:violation-recorded.window="
                        violated = true;
                        clearTimeout(timer);
                        timer = setTimeout(() => violated = false, 3000);
                    "
                >
                    <span
                        x-show="!violated"
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
                    >
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Focused
                    </span>
                    <span
                        x-show="violated"
                        x-cloak
                        class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400"
                    >
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-red-500"></span> Tab switch detected
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Internet Blocked Banner --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if ($policy && ! $policy->internet_access)
        <div class="border-b border-amber-300 bg-amber-50 px-4 py-2 dark:border-amber-700 dark:bg-amber-900/20">
            <div class="mx-auto flex max-w-7xl items-center gap-2 text-sm text-amber-800 dark:text-amber-300">
                <flux:icon.exclamation-triangle class="h-4 w-4 shrink-0" />
                Internet access is restricted during this session. Only shared resources are available.
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Violation Toast — Alpine-driven --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div
        x-data="{
            toasts: [],
            add(level, message) {
                const colors = {
                    1: 'bg-amber-500',
                    2: 'bg-orange-600',
                };
                const color = colors[level] ?? 'bg-red-700';
                const id = Date.now();
                this.toasts.push({ id, level, message, color });
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        x-on:violation-recorded.window="add($event.detail.level, $event.detail.message)"
        class="fixed right-4 top-20 z-50 flex flex-col gap-2"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                :class="toast.color"
                class="max-w-xs rounded-xl px-4 py-3 text-white shadow-xl"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <p class="text-sm font-semibold" x-text="toast.message"></p>
            </div>
        </template>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Main Content --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="mx-auto w-full max-w-7xl px-4 pt-6 pb-2">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ─── LEFT COLUMN — Resources + Viewer (wide) ──────── --}}
            <div class="lg:col-span-2 flex flex-col gap-4 pb-6">

                {{-- Shared Resources list --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="mb-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Shared Resources</h2>

                    @if ($sessionData->resources->isEmpty())
                        <p class="py-4 text-center text-xs text-zinc-400">No resources shared yet.</p>
                    @else
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($sessionData->resources as $resource)
                                <button
                                    wire:click="openResource({{ $resource->id }})"
                                    wire:key="resource-{{ $resource->id }}"
                                    class="flex w-full items-start justify-between rounded-lg border px-3 py-2.5 text-left transition hover:bg-zinc-50 dark:hover:bg-zinc-800
                                        {{ $activeResourceId === $resource->id
                                            ? 'border-sky-400 bg-sky-50 dark:border-sky-700 dark:bg-sky-900/20'
                                            : 'border-zinc-200 dark:border-zinc-700' }}"
                                >
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $resource->title }}</p>
                                        <p class="text-xs text-zinc-400">{{ $resource->created_at->diffForHumans() }}</p>
                                    </div>
                                    <flux:badge
                                        size="sm"
                                        color="{{ $resource->rendering_mode === 'external' ? 'amber' : 'zinc' }}"
                                        class="ml-2 shrink-0"
                                    >{{ $resource->rendering_mode }}</flux:badge>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Active Resource Viewer — full width here gives iframe plenty of room --}}
                @if ($activeResource)
                    <div x-data="{ isFullscreen: false }" 
                         :class="isFullscreen ? 'fixed inset-0 z-50 flex flex-col bg-zinc-950 p-4 sm:p-6' : 'rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900'">
                        
                        <div class="mb-3 flex items-center justify-between shrink-0">
                            <h2 class="text-sm font-semibold" :class="isFullscreen ? 'text-zinc-200' : 'text-zinc-700 dark:text-zinc-300'">{{ $activeResource->title }}</h2>
                            <div class="flex items-center gap-3">
                                <button @click="isFullscreen = !isFullscreen" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200" title="Toggle Fullscreen">
                                    <flux:icon.arrows-pointing-out x-show="!isFullscreen" class="h-4 w-4" />
                                    <flux:icon.arrows-pointing-in x-show="isFullscreen" class="h-4 w-4" style="display: none;" />
                                </button>
                                <button wire:click="$set('activeResourceId', null)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200" title="Close Resource">
                                    <flux:icon.x-mark class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 relative">
                            @if ($activeResource->rendering_mode === 'external')
                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-6 text-center dark:border-amber-700 dark:bg-amber-900/10 h-full flex flex-col items-center justify-center min-h-[400px]">
                                    <flux:icon.arrow-top-right-on-square class="mx-auto mb-3 h-10 w-10 text-amber-500" />
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">This resource opens in a new tab.</p>
                                    <p class="mt-1 text-xs text-zinc-500">Focus monitoring will pause for 30 seconds.</p>
                                    <button
                                        x-data
                                        x-on:click="$wire.pauseFocus(30).then(() => window.open('{{ $activeResource->accessUrl() }}', '_blank'))"
                                        class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-5 py-2 text-sm font-medium text-white hover:bg-amber-600"
                                    >
                                        <flux:icon.arrow-top-right-on-square class="h-4 w-4" /> Open Link
                                    </button>
                                </div>
                            @elseif ($activeResource->rendering_mode === 'iframe')
                                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 h-full">
                                    <iframe
                                        src="{{ $activeResource->accessUrl() }}"
                                        sandbox="allow-scripts allow-same-origin allow-forms"
                                        :class="isFullscreen ? 'h-full w-full' : 'h-[600px] w-full'"
                                        loading="lazy"
                                    ></iframe>
                                </div>
                            @elseif ($activeResource->rendering_mode === 'pdfjs')
                                {{-- Built-in browser PDF viewer via iframe --}}
                                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 h-full">
                                    <iframe
                                        src="{{ $activeResource->accessUrl() }}"
                                        :class="isFullscreen ? 'h-full w-full' : 'h-[600px] w-full'"
                                        type="application/pdf"
                                        loading="lazy"
                                    ></iframe>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>{{-- /left column --}}

            {{-- ─── RIGHT COLUMN — Announcements + Status (narrow) ── --}}
            <div class="flex flex-col gap-4 sticky top-6 max-h-[calc(100vh-120px)] overflow-y-auto pr-2 pb-6">

                {{-- Announcements --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="mb-4 flex items-center gap-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        <flux:icon.megaphone class="h-4 w-4" /> Announcements
                    </h2>

                    @if ($announcements->isEmpty())
                        <p class="py-4 text-center text-xs text-zinc-400">No announcements yet.</p>
                    @else
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                            @foreach ($announcements as $log)
                                <div class="rounded-lg border border-sky-100 bg-sky-50 px-4 py-3 dark:border-sky-900/40 dark:bg-sky-900/10">
                                    <p class="text-sm text-zinc-800 dark:text-zinc-200">
                                        {{ $log->metadata['announcement'] ?? $log->description }}
                                    </p>
                                    <p class="mt-1 text-xs text-zinc-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Your status --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="mb-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Your Status</h2>

                    @php
                        $totalViolations = $sessionDevice?->violation_count ?? 0;
                        $warningLevel    = $sessionDevice?->warningLevel() ?? 0;
                        $cycleColor = match($warningLevel) {
                            3       => 'text-red-600 dark:text-red-400',
                            2       => 'text-orange-500 dark:text-orange-400',
                            1       => 'text-amber-500 dark:text-amber-400',
                            default => 'text-emerald-600 dark:text-emerald-400',
                        };
                        $warningLabel = match($warningLevel) {
                            1       => 'Warning',
                            2       => 'One more will lock',
                            3       => 'Locked',
                            default => 'Good',
                        };
                    @endphp

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-zinc-500">Total Violations</p>
                            <p class="mt-0.5 text-2xl font-bold text-zinc-700 dark:text-zinc-300">{{ $totalViolations }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500">Current Cycle</p>
                            <p class="mt-0.5 text-sm font-semibold {{ $cycleColor }}">{{ $warningLabel }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500">Joined</p>
                            <p class="mt-0.5 text-xs text-zinc-700 dark:text-zinc-300">{{ $sessionDevice?->joined_at?->diffForHumans() ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500">Focus Monitor</p>
                            <p class="mt-0.5 text-xs font-medium {{ $focusPaused ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $focusPaused ? 'Paused' : 'Active' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>{{-- /right column --}}
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- JavaScript: Timer + Focus Enforcement --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@script
<script>
    // ── Session duration timer ──────────────────────────────────────
    (function () {
        const el = document.getElementById('student-session-timer');
        if (!el) return;

        const startedAt = new Date(el.dataset.startedAt);
        function pad(n) { return String(n).padStart(2, '0'); }
        function tick() {
            const s = Math.floor(Math.max(0, Date.now() - startedAt.getTime()) / 1000);
            el.textContent = pad(Math.floor(s / 3600)) + ':' + pad(Math.floor((s % 3600) / 60)) + ':' + pad(s % 60);
        }
        tick();
        setInterval(tick, 1000);
    })();


    // ── Focus enforcement ───────────────────────────────────────────
    // Cooldown prevents double-counting when visibilitychange + blur both fire
    let violationCooldown = false;

    function maybeReportViolation(type) {
        // Read focusPaused directly from the Livewire wire object
        if ($wire.focusPaused || violationCooldown) return;

        violationCooldown = true;
        setTimeout(() => { violationCooldown = false; }, 2000);

        $wire.reportViolation(type);
    }

    // Tab switch detection (most reliable signal)
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            maybeReportViolation('tab_switch');
        }
    });

    // Window blur — fires when student alt-tabs or clicks browser chrome
    // Small delay to avoid doubling with visibilitychange on same event
    window.addEventListener('blur', () => {
        // If the focus moved to an iframe inside our own page, do NOT report a violation!
        // This is crucial because clicking inside a cross-origin iframe fires a window blur.
        if (document.activeElement && document.activeElement.tagName === 'IFRAME') {
            return;
        }
        maybeReportViolation('window_blur');
    });

    // Fullscreen enforcement
    let fullscreenRequested = false;

    try {
        const workspace = document.getElementById('session-workspace');
        if (workspace && workspace.requestFullscreen) {
            workspace.requestFullscreen()
                .then(() => { fullscreenRequested = true; })
                .catch(() => { fullscreenRequested = true; }); // denied — don't penalise
        } else {
            fullscreenRequested = true;
        }
    } catch (e) {
        fullscreenRequested = true;
    }

    document.addEventListener('fullscreenchange', function () {
        // Only report exit AFTER we have successfully entered fullscreen
        if (fullscreenRequested && !document.fullscreenElement) {
            maybeReportViolation('fullscreen_exit');
        }
    });
</script>
@endscript
