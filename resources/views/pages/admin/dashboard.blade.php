<x-layouts.admin title="Dashboard">

    {{-- Stats row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Total Users</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ \App\Models\User::count() }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                {{ \App\Models\User::role('teacher')->count() }} teachers ·
                {{ \App\Models\User::role('student')->count() }} students
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Devices</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ \App\Models\Device::count() }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                {{ \App\Models\Device::where('status', 'pending')->count() }} pending approval
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Classrooms</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ \App\Models\Classroom::count() }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                {{ \App\Models\Classroom::where('is_active', true)->count() }} active
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Active Sessions</p>
            <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-1">
                {{ \App\Models\ClassSession::where('status', 'active')->count() }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                {{ \App\Models\ClassSession::where('status', 'ended')->count() }} total ended
            </p>
        </div>

    </div>

    {{-- Recent activity + pending devices --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Pending device approvals --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Pending Device Approvals</h2>
                <a href="{{ route('admin.devices.index') }}"
                   class="text-xs text-indigo-600 hover:text-indigo-700">View all</a>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse(\App\Models\Device::with('user')->where('status','pending')->latest()->take(5)->get() as $device)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $device->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $device->user->name }} · {{ $device->device_type }}</p>
                        </div>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Pending</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-zinc-400">
                        No pending approvals
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent activity logs --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Recent Activity</h2>
                <a href="{{ route('admin.logs.index') }}"
                   class="text-xs text-indigo-600 hover:text-indigo-700">View all</a>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse(\App\Models\ActivityLog::with('user')->latest('created_at')->take(6)->get() as $log)
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-mono text-indigo-600">{{ $log->action }}</p>
                            <p class="text-xs text-zinc-400">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            {{ $log->user?->name ?? 'System' }}
                            @if($log->description) — {{ $log->description }} @endif
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-zinc-400">
                        No activity yet
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</x-layouts.admin>