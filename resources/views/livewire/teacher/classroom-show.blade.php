<div class="space-y-6">

    {{-- Header card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ $classroom->name }}
                    </h2>
                    @if($activeSession)
                        <span class="flex items-center gap-1.5 text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            Live
                        </span>
                    @endif
                </div>
                <p class="text-sm text-zinc-500">
                    {{ $classroom->subject }} ·
                    {{ $classroom->students->count() }} students ·
                    Join code:
                    <span class="font-mono font-semibold text-zinc-700 dark:text-zinc-300 tracking-wider">
                        {{ $classroom->join_code }}
                    </span>
                </p>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                @if($activeSession)
                    <a href="{{ route('teacher.sessions.live', $activeSession) }}"
                       class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors font-medium flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                        Rejoin Session
                    </a>
                    <button
                        wire:click="endSession({{ $activeSession->id }})"
                        wire:confirm="End the session for all connected students?"
                        class="text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        End Session
                    </button>
                @else
                    <button
                        wire:click="$set('showSessionForm', true)"
                        class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                        + Start Session
                    </button>
                @endif
            </div>
        </div>

        {{-- Start session form --}}
        @if($showSessionForm)
            <div class="mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-700">
                <form wire:submit="startSession" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Session title
                        </label>
                        <input
                            wire:model="sessionTitle"
                            type="text"
                            placeholder="e.g. Chapter 5 — Computer Networks"
                            autofocus
                            class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                        />
                        @error('sessionTitle')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors font-medium flex-shrink-0">
                        Start
                    </button>
                    <button type="button"
                        wire:click="$set('showSessionForm', false)"
                        class="text-sm text-zinc-500 hover:text-zinc-700 px-3 py-2 flex-shrink-0">
                        Cancel
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Students list --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                    Enrolled Students ({{ $classroom->students->count() }})
                </h3>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($classroom->students as $student)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">
                                    {{ substr($student->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $student->name }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    {{ $student->email }}
                                    @php $device = $student->devices->first(); @endphp
                                    @if($device)
                                        · {{ $device->name }}
                                        <span class="{{ $device->status === 'approved' ? 'text-emerald-500' : 'text-amber-500' }}">
                                            ({{ $device->status }})
                                        </span>
                                    @else
                                        · <span class="text-red-400">No device</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                            <button
                                wire:click="removeStudent({{ $student->id }})"
                                wire:confirm="Remove this student from the classroom?"
                                class="text-xs text-zinc-400 hover:text-red-500 transition-colors p-1">
                                <flux:icon.x-mark class="w-4 h-4" />
                            </button>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <div class="rounded-full bg-zinc-100 p-3 dark:bg-zinc-800 inline-block mb-3">
                            <flux:icon.users class="h-6 w-6 text-zinc-400 dark:text-zinc-500" />
                        </div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">No students enrolled</p>
                        <p class="mt-1 text-xs text-zinc-500">Share the join code <span class="font-mono font-semibold text-zinc-600 dark:text-zinc-300">{{ $classroom->join_code }}</span> with your students.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-4">

            {{-- Recent sessions --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Recent Sessions</h3>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @forelse($classroom->sessions as $session)
                        <div class="px-5 py-3">
                            <div class="flex items-center justify-between mb-0.5">
                                <p class="text-xs font-medium text-zinc-900 dark:text-white truncate pr-2">
                                    {{ $session->title }}
                                </p>
                                @if($session->status === 'active')
                                    <span class="text-xs bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full flex-shrink-0">Live</span>
                                @elseif($session->status === 'ended')
                                    <span class="text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-500 px-1.5 py-0.5 rounded-full flex-shrink-0">Ended</span>
                                @else
                                    <span class="text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full flex-shrink-0">Scheduled</span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-400">
                                {{ $session->created_at->diffForHumans() }}
                                @if($session->duration()) · {{ $session->duration() }} @endif
                            </p>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <div class="rounded-full bg-zinc-100 p-2 dark:bg-zinc-800 inline-block mb-2">
                                <flux:icon.video-camera class="h-5 w-5 text-zinc-400 dark:text-zinc-500" />
                            </div>
                            <p class="text-xs font-medium text-zinc-900 dark:text-zinc-100">No sessions yet</p>
                            <p class="mt-0.5 text-xs text-zinc-500">Start your first session.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Policies --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Policies</h3>
                    <a href="{{ route('teacher.policies.index', $classroom) }}"
                       class="text-xs text-emerald-600 hover:text-emerald-700">Manage</a>
                </div>
                <div class="px-5 py-3">
                    @forelse($classroom->policies as $policy)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-xs text-zinc-700 dark:text-zinc-300">{{ $policy->name }}</span>
                            @if($policy->is_default)
                                <span class="text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded-full">Default</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-zinc-400 py-2">No policies configured</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>