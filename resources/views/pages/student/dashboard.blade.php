<x-layouts.student title="Dashboard">

    {{-- Device status alert --}}
    @php $device = auth()->user()->approvedDevice(); @endphp

    @if(!$device)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <flux:icon.exclamation-triangle class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" />
            <div>
                <p class="text-sm font-medium text-amber-800">No approved device</p>
                <p class="text-xs text-amber-600 mt-0.5">
                    You need an approved device to join sessions.
                    <a href="{{ route('student.device') }}" class="underline font-medium">Register your device →</a>
                </p>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">My Classrooms</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ auth()->user()->enrolledClassrooms()->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Sessions Attended</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ $device ? $device->sessions()->where('status','ended')->count() : 0 }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Device Status</p>
            <p class="text-lg font-bold mt-1 {{ $device ? 'text-emerald-600' : 'text-amber-500' }}">
                {{ $device ? ucfirst($device->status) : 'Not registered' }}
            </p>
        </div>

    </div>

    {{-- Active sessions alert --}}
    @php
        $activeSessions = \App\Models\ClassSession::whereHas('classroom', function($q) {
            $q->whereHas('students', fn($s) => $s->where('users.id', auth()->id()));
        })->where('status', 'active')->with('classroom')->get();
    @endphp

    @if($activeSessions->count() > 0)
        <div class="mb-6">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                Live Sessions Right Now
            </h2>
            @foreach($activeSessions as $session)
                <div class="bg-emerald-50 border border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-emerald-900 dark:text-emerald-100">
                            {{ $session->title }}
                        </p>
                        <p class="text-xs text-emerald-600 mt-0.5">
                            {{ $session->classroom->name }}
                        </p>
                    </div>
                    <a href="{{ route('student.sessions.live', $session) }}"
                       class="text-sm bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                        Join Now
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Enrolled classrooms --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">My Classrooms</h2>
            <a href="{{ route('student.join') }}"
               class="text-xs bg-sky-600 text-white px-3 py-1.5 rounded-lg hover:bg-sky-700 transition-colors">
                + Join Classroom
            </a>
        </div>
        <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
            @forelse(auth()->user()->enrolledClassrooms()->with('teacher')->get() as $classroom)
                <a href="{{ route('student.classrooms.show', $classroom) }}"
                   class="px-5 py-4 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors block">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $classroom->name }}</p>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            {{ $classroom->subject }} · {{ $classroom->teacher->name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($classroom->hasActiveSession())
                            <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Live
                            </span>
                        @endif
                        <flux:icon.chevron-right class="w-4 h-4 text-zinc-400" />
                    </div>
                </a>
            @empty
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-zinc-400 mb-3">Not enrolled in any classrooms yet</p>
                    <a href="{{ route('student.join') }}"
                       class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                        Join a classroom with a code →
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</x-layouts.student>