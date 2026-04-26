<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 mb-4">
            <p class="text-sm text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search devices or students..."
            class="flex-1 px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />
        <select
            wire:model.live="filterStatus"
            class="px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
            <option value="all">All statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="blocked">Blocked</option>
        </select>
    </div>

    {{-- Device table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Device</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Student</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Type</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Registered</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($devices as $device)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-zinc-900 dark:text-white">{{ $device->name }}</p>
                            @if($device->mac_address)
                                <p class="text-xs font-mono text-zinc-400">{{ $device->mac_address }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-zinc-600 dark:text-zinc-300">
                            {{ $device->user->name }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 px-2 py-0.5 rounded-full capitalize">
                                {{ $device->device_type }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($device->status === 'approved')
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Approved</span>
                            @elseif($device->status === 'pending')
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Pending</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Blocked</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-zinc-400">
                            {{ $device->registered_at->diffForHumans() }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @if($device->status !== 'approved')
                                    <button
                                        wire:click="approve({{ $device->id }})"
                                        wire:confirm="Approve this device?"
                                        class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        Approve
                                    </button>
                                @endif
                                @if($device->status !== 'blocked')
                                    <button
                                        wire:click="block({{ $device->id }})"
                                        wire:confirm="Block this device?"
                                        class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        Block
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-zinc-400">
                            No devices found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($devices->hasPages())
            <div class="px-5 py-3 border-t border-zinc-200 dark:border-zinc-700">
                {{ $devices->links() }}
            </div>
        @endif
    </div>
</div>