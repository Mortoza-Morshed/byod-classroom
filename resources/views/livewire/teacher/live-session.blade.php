<div wire:poll.5s class="space-y-6">

    {{-- Flash messages --}}
    @if (session('message'))
        <flux:callout variant="success" icon="check-circle">{{ session('message') }}</flux:callout>
    @endif
    @if (session('announcement_sent'))
        <flux:callout variant="success" icon="megaphone">{{ session('announcement_sent') }}</flux:callout>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Top Bar --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="space-y-0.5">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $session->title }}</h1>
                {{-- Pulsing LIVE badge --}}
                <span class="relative flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-400">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-red-500"></span>
                    </span>
                    LIVE
                </span>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $session->classroom->name }}</p>
        </div>

        <div class="flex items-center gap-4">
            {{-- JS-driven duration timer — reads started_at from data attribute --}}
            {{-- wire:ignore prevents Livewire from morphing this element on poll cycles --}}
            <div class="text-center" wire:ignore>
                <p
                    id="session-timer"
                    data-started-at="{{ $session->started_at?->toISOString() }}"
                    class="font-mono text-2xl font-bold text-emerald-600 dark:text-emerald-400"
                >00:00:00</p>
                <p class="text-xs text-zinc-500">Duration</p>
            </div>

            <flux:button
                wire:click="endSession"
                wire:confirm="End session for all students? This cannot be undone."
                variant="danger"
                icon="stop-circle"
            >
                End Session
            </flux:button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Main grid --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ─── LEFT COLUMN ─────────────────────────────────────────── --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Stats row --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Active Devices</p>
                    <p class="mt-1 text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $activeDevices }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                    <p class="text-xs font-medium text-amber-700 dark:text-amber-400">Total Violations</p>
                    <p class="mt-1 text-3xl font-bold text-amber-700 dark:text-amber-400">{{ $violationCount }}</p>
                </div>
                <div @class([
                    'rounded-xl border p-4',
                    'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' => $lockedCount > 0,
                    'border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800' => $lockedCount === 0,
                ])>
                    <p @class([
                        'text-xs font-medium',
                        'text-red-700 dark:text-red-400' => $lockedCount > 0,
                        'text-zinc-600 dark:text-zinc-400' => $lockedCount === 0,
                    ])>Locked Devices</p>
                    <p @class([
                        'mt-1 text-3xl font-bold',
                        'text-red-700 dark:text-red-400' => $lockedCount > 0,
                        'text-zinc-700 dark:text-zinc-300' => $lockedCount === 0,
                    ])>{{ $lockedCount }}</p>
                </div>
            </div>

            {{-- Active policy banner --}}
            @if ($defaultPolicy)
                <div @class([
                    'flex items-start gap-3 rounded-xl border p-4',
                    'border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20' => ! $defaultPolicy->internet_access,
                    'border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800' => $defaultPolicy->internet_access,
                ])>
                    <flux:icon.shield-check class="mt-0.5 h-5 w-5 shrink-0 text-zinc-500 dark:text-zinc-400" />
                    <div class="flex-1 text-sm">
                        <p class="font-semibold text-zinc-800 dark:text-zinc-200">Active Policy: {{ $defaultPolicy->name }}</p>
                        <p class="mt-0.5 text-zinc-500 dark:text-zinc-400">
                            @if ($defaultPolicy->internet_access)
                                Internet allowed &mdash; {{ count($defaultPolicy->allowed_urls ?? []) }} URL restriction(s)
                            @else
                                <span class="font-medium text-amber-700 dark:text-amber-400">Internet blocked for all students</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- Announcement bar --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <flux:icon.megaphone class="h-4 w-4" /> Announcements
                    </div>
                    @if (! $showAnnouncement)
                        <flux:button wire:click="$set('showAnnouncement', true)" size="sm" variant="ghost" icon="plus">Make Announcement</flux:button>
                    @endif
                </div>

                @if ($showAnnouncement)
                    <form wire:submit="pushAnnouncement" class="mt-3 flex gap-2">
                        <flux:input wire:model="announcement" placeholder="Type your announcement…" class="flex-1" required />
                        <flux:button type="submit" variant="primary" size="sm">Send</flux:button>
                        <flux:button type="button" wire:click="$set('showAnnouncement', false)" variant="ghost" size="sm">Cancel</flux:button>
                    </form>
                    <flux:error name="announcement" class="mt-1 text-xs" />
                @endif
            </div>

            {{-- Device grid --}}
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Connected Devices</h2>
                    <div class="flex gap-2">
                        <flux:button wire:click="lockAll" size="sm" variant="danger" icon="lock-closed">Lock All</flux:button>
                        <flux:button wire:click="unlockAll" size="sm" variant="ghost" icon="lock-open">Unlock All</flux:button>
                    </div>
                </div>

                @if ($sessionDevices->isEmpty())
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 py-10 text-center dark:border-zinc-700 dark:bg-zinc-800">
                        <flux:icon.device-tablet class="mx-auto mb-2 h-10 w-10 text-zinc-400" />
                        <p class="text-sm text-zinc-500">No devices connected yet.</p>
                    </div>
                @else
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($sessionDevices as $sd)
                            @php
                                $violations = $sd->violation_count;
                                $violationColor = match(true) {
                                    $violations >= 2 => 'text-red-600 dark:text-red-400',
                                    $violations === 1 => 'text-amber-600 dark:text-amber-400',
                                    default           => 'text-emerald-600 dark:text-emerald-400',
                                };
                                $level = $sd->warningLevel();
                            @endphp
                            <div @class([
                                'rounded-xl border p-4 text-sm transition',
                                'border-red-300 bg-red-50 dark:border-red-800 dark:bg-red-900/10' => $sd->is_locked,
                                'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900' => ! $sd->is_locked,
                            ])>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-zinc-900 dark:text-zinc-100">{{ $sd->device->user->name }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $sd->device->name }}</p>
                                        <p class="mt-1 text-xs text-zinc-400">Joined {{ $sd->joined_at?->diffForHumans() ?? '—' }}</p>
                                    </div>
                                    <div class="shrink-0 space-y-1 text-right">
                                        @if ($sd->is_locked)
                                            <flux:badge color="red" size="sm">Locked</flux:badge>
                                        @endif
                                        <p class="{{ $violationColor }} text-xs font-medium">{{ $violations }} violation(s)</p>
                                    </div>
                                </div>

                                {{-- Warning level dots --}}
                                <div class="mt-2 flex items-center gap-1">
                                    @for ($dot = 1; $dot <= 3; $dot++)
                                        <span @class([
                                            'h-2.5 w-2.5 rounded-full',
                                            'bg-red-500' => $dot <= $level && $level === 3,
                                            'bg-amber-400' => $dot <= $level && $level === 2,
                                            'bg-yellow-300' => $dot <= $level && $level === 1,
                                            'bg-zinc-200 dark:bg-zinc-700' => $dot > $level,
                                        ])></span>
                                    @endfor
                                    <span class="ml-1 text-xs text-zinc-400">Warning {{ $level }}/3</span>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    @if ($sd->is_locked)
                                        <flux:button wire:click="unlockDevice({{ $sd->id }})" size="sm" variant="ghost" icon="lock-open">Unlock</flux:button>
                                    @else
                                        <flux:button wire:click="lockDevice({{ $sd->id }})" size="sm" variant="danger" icon="lock-closed">Lock</flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>{{-- /left column --}}

        {{-- ─── RIGHT COLUMN ────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Share Resource Panel --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Share Resource</h2>
                    @if (! $showResourceForm)
                        <flux:button wire:click="$set('showResourceForm', true)" size="sm" variant="ghost" icon="plus">Share</flux:button>
                    @endif
                </div>

                @if ($showResourceForm)
                    {{-- Mode tabs --}}
                    <div class="mt-3 flex rounded-lg border border-zinc-200 p-0.5 dark:border-zinc-700">
                        <button
                            type="button"
                            wire:click="$set('resourceType', 'link')"
                            @class([
                                'flex-1 rounded-md px-3 py-1.5 text-xs font-medium transition',
                                'bg-emerald-600 text-white shadow-sm' => $resourceType === 'link',
                                'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' => $resourceType !== 'link',
                            ])
                        >Share Link</button>
                        <button
                            type="button"
                            wire:click="$set('resourceType', 'file')"
                            @class([
                                'flex-1 rounded-md px-3 py-1.5 text-xs font-medium transition',
                                'bg-emerald-600 text-white shadow-sm' => $resourceType === 'file',
                                'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' => $resourceType !== 'file',
                            ])
                        >Upload PDF</button>
                    </div>

                    <form wire:submit="shareResource" class="mt-3 space-y-3">
                        {{-- Title (shared by both modes) --}}
                        <flux:field>
                            <flux:label>Title</flux:label>
                            <flux:input wire:model="resourceTitle" placeholder="e.g. Chapter 5 Notes" required />
                            <flux:error name="resourceTitle" />
                        </flux:field>

                        @if ($resourceType === 'link')
                            <flux:field>
                                <flux:label>URL</flux:label>
                                <flux:input wire:model="resourceUrl" type="url" placeholder="https://…" required />
                                <flux:description>Google/YouTube links will open externally.</flux:description>
                                <flux:error name="resourceUrl" />
                            </flux:field>
                        @else
                            {{-- File upload mode --}}
                            <div
                                x-data="{
                                    fileName: null,
                                    progress: 0,
                                    uploading: false,
                                }"
                                x-on:livewire-upload-start="uploading = true; progress = 0"
                                x-on:livewire-upload-finish="uploading = false"
                                x-on:livewire-upload-error="uploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >
                                <label class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center transition hover:border-emerald-400 hover:bg-emerald-50/30 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-emerald-600 dark:hover:bg-emerald-900/10 cursor-pointer">
                                    <flux:icon.document-arrow-up class="h-8 w-8 text-zinc-400" />
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                        <span x-show="!fileName">Click to select a PDF file</span>
                                        <span x-show="fileName" x-text="fileName" class="font-medium text-emerald-600 dark:text-emerald-400"></span>
                                    </span>
                                    <span class="text-[10px] text-zinc-400">PDF only · max 10 MB</span>
                                    <input
                                        type="file"
                                        wire:model="resourceFile"
                                        accept=".pdf,application/pdf"
                                        class="sr-only"
                                        x-on:change="fileName = $event.target.files[0]?.name ?? null"
                                    />
                                </label>

                                {{-- Upload progress bar --}}
                                <div x-show="uploading" class="mt-2">
                                    <div class="flex items-center justify-between text-xs text-zinc-500 mb-1">
                                        <span>Uploading…</span>
                                        <span x-text="progress + '%'"></span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700">
                                        <div
                                            class="h-1.5 rounded-full bg-emerald-600 transition-all"
                                            :style="'width: ' + progress + '%'"
                                        ></div>
                                    </div>
                                </div>

                                <flux:error name="resourceFile" class="mt-1" />
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <flux:button type="submit" variant="primary" size="sm" icon="share">Share</flux:button>
                            <flux:button type="button" wire:click="$set('showResourceForm', false)" variant="ghost" size="sm">Cancel</flux:button>
                        </div>
                    </form>
                @endif

                {{-- Shared resources list --}}
                <div class="mt-4 space-y-2">
                    @forelse ($resources as $resource)
                        <div class="rounded-lg border border-zinc-100 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $resource->title }}</p>
                                <flux:badge
                                    size="sm"
                                    color="{{ $resource->rendering_mode === 'external' ? 'amber' : 'zinc' }}"
                                >{{ $resource->rendering_mode }}</flux:badge>
                            </div>
                            <p class="mt-0.5 text-xs text-zinc-400">{{ $resource->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-zinc-400">No resources shared yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Live activity feed --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Live Activity Feed</h2>
                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @forelse ($activityLogs as $log)
                        @php
                            $actionColor = match(true) {
                                str_starts_with($log->action, 'focus.')    => 'text-red-500',
                                str_starts_with($log->action, 'session.')  => 'text-blue-500',
                                str_starts_with($log->action, 'resource.') => 'text-emerald-500',
                                str_starts_with($log->action, 'device.')   => 'text-amber-500',
                                default                                     => 'text-zinc-400',
                            };
                        @endphp
                        <div class="rounded-lg border border-zinc-100 bg-zinc-50 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                            {{-- Description is the prominent text the teacher reads --}}
                            @if ($log->description)
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $log->description }}</p>
                            @endif
                            {{-- Action string is small, muted, monospace secondary label --}}
                            <p class="mt-0.5 font-mono text-[10px] {{ $actionColor }} opacity-80">{{ $log->action }}</p>
                            <p class="mt-0.5 text-xs text-zinc-400">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-zinc-400">No activity yet.</p>
                    @endforelse
                </div>
            </div>

        </div>{{-- /right column --}}

    </div>
</div>

<script>
    (function () {
        const el = document.getElementById('session-timer');
        if (!el) return;

        const startedAt = new Date(el.dataset.startedAt);

        function pad(n) { return String(n).padStart(2, '0'); }

        function tick() {
            const now = new Date();
            const diffMs = Math.max(0, now - startedAt);
            const totalSeconds = Math.floor(diffMs / 1000);
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            el.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
