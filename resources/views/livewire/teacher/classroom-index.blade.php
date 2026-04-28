<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $classrooms->count() }} {{ Str::plural('classroom', $classrooms->count()) }} total
            </h2>
        </div>
        <a href="{{ route('teacher.classrooms.create') }}"
           class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
            + New Classroom
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($classrooms as $classroom)
            <a href="{{ route('teacher.classrooms.show', $classroom) }}"
               class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-sm transition-all block group">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <flux:icon.building-library class="w-5 h-5 text-emerald-600" />
                    </div>
                    @if($classroom->sessions->count() > 0)
                        <span class="flex items-center gap-1.5 text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-1 rounded-full font-medium">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            Live
                        </span>
                    @else
                        <span class="text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-500 px-2 py-1 rounded-full">
                            No active session
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors mb-1">
                    {{ $classroom->name }}
                </h3>
                <p class="text-xs text-zinc-500 mb-4">{{ $classroom->subject }}</p>

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-700">
                    <div class="flex items-center gap-1.5 text-xs text-zinc-500">
                        <flux:icon.users class="w-3.5 h-3.5" />
                        {{ $classroom->students_count }} students
                    </div>
                    <div class="flex items-center gap-1 text-xs text-zinc-400">
                        <span>Code:</span>
                        <span class="font-mono font-semibold text-zinc-600 dark:text-zinc-300">
                            {{ $classroom->join_code }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-3 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                <flux:icon.building-library class="w-10 h-10 text-zinc-300 mx-auto mb-3" />
                <p class="text-sm font-medium text-zinc-500 mb-1">No classrooms yet</p>
                <p class="text-xs text-zinc-400 mb-4">Create your first classroom to get started</p>
                <a href="{{ route('teacher.classrooms.create') }}"
                   class="text-sm bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">
                    Create Classroom
                </a>
            </div>
        @endforelse
    </div>
</div>