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
    <div wire:loading.class="opacity-50" class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden transition-opacity duration-300">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Device</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Student</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wide">Reg ID</th>
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
                            <span class="text-xs font-mono font-medium text-zinc-700 dark:text-zinc-200">
                                {{ $device->user->registration_id ?? '—' }}
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
                                        wire:confirm="Block this device? The student will lose session access."
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
                        <td colspan="6" class="px-5 py-16">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="rounded-full bg-zinc-100 p-3 dark:bg-zinc-800">
                                    <flux:icon.device-phone-mobile class="h-6 w-6 text-zinc-400 dark:text-zinc-500" />
                                </div>
                                <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">No devices found</p>
                                <p class="mt-1 text-xs text-zinc-500">No devices match your current filters.</p>
                                @if($search || $filterStatus !== 'all')
                                    <button wire:click="$set('search', ''); $set('filterStatus', 'all')" class="mt-4 text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">Clear filters</button>
                                @endif
                            </div>
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
