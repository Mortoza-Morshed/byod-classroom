<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $classroom->name }}</h1>
                @if($activeSession)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Session Live
                    </span>
                @endif
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $classroom->subject ? $classroom->subject . ' • ' : '' }}Teacher: {{ $classroom->teacher->name }}
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-4 py-2 flex items-center gap-3">
                <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium uppercase tracking-wider">Join Code</span>
                <span class="font-mono font-bold text-lg text-zinc-900 dark:text-zinc-100 tracking-widest">{{ $classroom->join_code }}</span>
            </div>
            
            @if($activeSession)
                <a href="{{ route('student.sessions.live', $activeSession) }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-offset-zinc-800 shadow-sm">
                    <flux:icon.play class="w-4 h-4" />
                    Join Session
                </a>
            @endif
        </div>
    </div>

    @if($activeSession)
        {{-- Active Session Banner Prompt --}}
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="bg-emerald-100 dark:bg-emerald-800/50 p-3 rounded-full text-emerald-600 dark:text-emerald-400 shrink-0">
                    <flux:icon.presentation-chart-bar class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-medium text-emerald-900 dark:text-emerald-100 mb-1">A class session is currently active</h3>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">Join the session to access resources and participate in class activities.</p>
                </div>
            </div>
            <a href="{{ route('student.sessions.live', $activeSession) }}" class="shrink-0 w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-offset-emerald-900 shadow-sm">
                <flux:icon.arrow-right-end-on-rectangle class="w-5 h-5" />
                Join Now
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Session History --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <flux:icon.clock class="w-5 h-5 text-zinc-400 dark:text-zinc-500" />
                        Recent Sessions
                    </h2>
                </div>
                
                @if($classroom->sessions->isEmpty())
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-zinc-100 dark:bg-zinc-900/50 rounded-full mb-3">
                            <flux:icon.calendar class="w-6 h-6 text-zinc-400 dark:text-zinc-500" />
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">No sessions have been held yet.</p>
                    </div>
                @else
                    <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($classroom->sessions as $session)
                            <li class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <div>
                                    <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $session->title }}</h4>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        <span class="flex items-center gap-1">
                                            <flux:icon.calendar-days class="w-3.5 h-3.5" />
                                            {{ $session->started_at->format('M j, Y') }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <flux:icon.clock class="w-3.5 h-3.5" />
                                            {{ $session->started_at->format('g:i A') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($session->isActive())
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <span class="relative flex h-1.5 w-1.5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                            </span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                            Ended
                                        </span>
                                        @if($session->duration())
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium bg-zinc-50 dark:bg-zinc-900 px-2 py-1 rounded border border-zinc-200 dark:border-zinc-700">
                                                {{ $session->duration() }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Right Column: Recent Resources --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <flux:icon.document-duplicate class="w-5 h-5 text-zinc-400 dark:text-zinc-500" />
                        Recent Resources
                    </h2>
                    @if($recentSession)
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-1">From: {{ $recentSession->title }}</p>
                    @endif
                </div>
                
                @if($resources->isEmpty())
                    <div class="p-6 text-center">
                        <div class="inline-flex items-center justify-center w-10 h-10 bg-zinc-100 dark:bg-zinc-900/50 rounded-full mb-2">
                            <flux:icon.folder class="w-5 h-5 text-zinc-400 dark:text-zinc-500" />
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">No resources available from the recent session.</p>
                    </div>
                @else
                    <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($resources as $resource)
                            <li class="px-6 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <a href="{{ $resource->accessUrl() }}" target="_blank" class="flex items-start gap-3 group">
                                    <div class="mt-0.5 p-1.5 rounded-md bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 group-hover:bg-sky-100 dark:group-hover:bg-sky-900/40 transition-colors">
                                        @if($resource->isFile())
                                            @if($resource->isPdf())
                                                <flux:icon.document-text class="w-4 h-4" />
                                            @else
                                                <flux:icon.document class="w-4 h-4" />
                                            @endif
                                        @else
                                            <flux:icon.link class="w-4 h-4" />
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                                            {{ $resource->title }}
                                        </p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">
                                            {{ $resource->isFile() ? 'File Document' : 'External Link' }}
                                        </p>
                                    </div>
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center self-center text-zinc-400 dark:text-zinc-500">
                                        <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
