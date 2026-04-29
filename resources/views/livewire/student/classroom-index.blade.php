<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Classrooms</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Classrooms you are enrolled in.</p>
        </div>
        <a href="{{ route('student.join') }}" class="flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg transition-colors font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-600 dark:focus:ring-offset-zinc-800">
            <flux:icon.plus class="w-4 h-4" />
            Join Classroom
        </a>
    </div>

    @if($classrooms->isEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-50 dark:bg-sky-900/20 rounded-full mb-4">
                <flux:icon.academic-cap class="w-8 h-8 text-sky-600 dark:text-sky-400" />
            </div>
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No classrooms yet</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto mb-6">You aren't enrolled in any classrooms. Ask your teacher for a 6-character join code.</p>
            <a href="{{ route('student.join') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 rounded-lg transition-colors font-medium text-sm">
                Join a Classroom
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classrooms as $classroom)
                @php
                    $activeSession = $classroom->sessions->first();
                @endphp
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm flex flex-col transition-all hover:shadow-md hover:border-sky-300 dark:hover:border-sky-700">
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg text-zinc-900 dark:text-zinc-100 line-clamp-1">
                                    <a href="{{ route('student.classrooms.show', $classroom) }}" class="hover:text-sky-600 dark:hover:text-sky-400">
                                        {{ $classroom->name }}
                                    </a>
                                </h3>
                                @if($classroom->subject)
                                    <p class="text-sm text-sky-600 dark:text-sky-400">{{ $classroom->subject }}</p>
                                @endif
                            </div>
                            @if($activeSession)
                                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-medium rounded-full border border-emerald-200 dark:border-emerald-500/20" title="Session is live">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    Live
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 mt-4 text-sm text-zinc-600 dark:text-zinc-400">
                            <div class="flex items-center gap-2">
                                <flux:icon.user class="w-4 h-4 text-zinc-400 dark:text-zinc-500" />
                                <span>Teacher: <span class="text-zinc-900 dark:text-zinc-200 font-medium">{{ $classroom->teacher->name }}</span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon.users class="w-4 h-4 text-zinc-400 dark:text-zinc-500" />
                                <span>{{ $classroom->students_count }} {{ Str::plural('student', $classroom->students_count) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 border-t border-zinc-200 dark:border-zinc-700">
                        @if($activeSession)
                            <a href="{{ route('student.sessions.live', $activeSession) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium text-sm">
                                <flux:icon.play class="w-4 h-4" />
                                Join Live Session
                            </a>
                        @else
                            <a href="{{ route('student.classrooms.show', $classroom) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg transition-colors font-medium text-sm">
                                View Classroom
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
