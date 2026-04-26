<x-layouts.teacher title="Dashboard">

    {{-- Welcome bar --}}
    <div class="bg-emerald-600 rounded-xl p-5 mb-6 text-white">
        <p class="text-sm opacity-80">Welcome back,</p>
        <p class="text-2xl font-bold">{{ auth()->user()->name }}</p>
        <p class="text-sm opacity-70 mt-1">
            {{ auth()->user()->classrooms()->count() }} classrooms ·
            {{ auth()->user()->classrooms()->withCount('students')->get()->sum('students_count') }} students enrolled
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">My Classrooms</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ auth()->user()->classrooms()->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Active Sessions</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ \App\Models\ClassSession::whereHas('classroom', fn($q) => $q->where('teacher_id', auth()->id()))->where('status','active')->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Sessions Run</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ \App\Models\ClassSession::whereHas('classroom', fn($q) => $q->where('teacher_id', auth()->id()))->where('status','ended')->count() }}
            </p>
        </div>

    </div>

    {{-- Classrooms list --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">My Classrooms</h2>
            <a href="{{ route('teacher.classrooms.create') }}"
               class="text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors">
                + New Classroom
            </a>
        </div>
        <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
            @forelse(auth()->user()->classrooms()->withCount('students')->with('sessions')->get() as $classroom)
                <a href="{{ route('teacher.classrooms.show', $classroom) }}"
                   class="px-5 py-4 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors block">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $classroom->name }}</p>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            {{ $classroom->subject }} ·
                            {{ $classroom->students_count }} students ·
                            Code: <span class="font-mono font-medium">{{ $classroom->join_code }}</span>
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
                    <p class="text-sm text-zinc-400 mb-3">No classrooms yet</p>
                    <a href="{{ route('teacher.classrooms.create') }}"
                       class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                        Create your first classroom →
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</x-layouts.teacher>