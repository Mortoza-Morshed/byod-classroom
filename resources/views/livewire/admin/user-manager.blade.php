<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">User Management</h1>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif
    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle">{{ session('error') }}</flux:callout>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search by name or email..." class="w-full sm:max-w-xs" />

        <div class="flex items-center gap-2">
            <select wire:model.live="filterRole" class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 w-36">
                <option value="all">All Roles ({{ $roleCounts['all'] }})</option>
                <option value="admin">Admins ({{ $roleCounts['admin'] }})</option>
                <option value="teacher">Teachers ({{ $roleCounts['teacher'] }})</option>
                <option value="student">Students ({{ $roleCounts['student'] }})</option>
            </select>

            <select wire:model.live="filterStatus" class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 w-32">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Devices</flux:table.column>
            <flux:table.column>Joined</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row wire:key="user-{{ $user->id }}">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-sm font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($user->roles as $role)
                                <flux:badge size="sm" color="{{ match($role->name) { 'admin' => 'indigo', 'teacher' => 'emerald', 'student' => 'sky', default => 'zinc' } }}">{{ ucfirst($role->name) }}</flux:badge>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $user->is_active ? 'emerald' : 'red' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-zinc-500">{{ $user->devices->count() }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-zinc-500">{{ $user->created_at->format('M j, Y') }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="p-1" />
                            <flux:menu>
                                <flux:menu.item icon="eye" href="{{ route('admin.users.show', $user) }}">View Details</flux:menu.item>
                                <flux:menu.separator />
                                @if ($user->id !== auth()->id())
                                    <flux:menu.item 
                                        wire:click="toggleActive({{ $user->id }})" 
                                        icon="{{ $user->is_active ? 'no-symbol' : 'check-circle' }}"
                                        class="{{ $user->is_active ? 'text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' }}"
                                    >
                                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                                    </flux:menu.item>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="py-8 text-center text-zinc-500">
                        No users found matching your search or filters.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
