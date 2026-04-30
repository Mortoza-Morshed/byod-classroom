<div class="space-y-6">
    @if (session('message'))
        <flux:callout variant="success" icon="check-circle">{{ session('message') }}</flux:callout>
    @endif

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">Classroom Policies</h2>
        @if (!$showForm)
            <flux:button wire:click="openCreate" icon="plus" variant="primary">New Policy</flux:button>
        @endif
    </div>

    <!-- Policy Form (Conditionally Rendered) -->
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ $editingPolicyId ? 'Edit Policy' : 'New Policy' }}</flux:heading>
            
            <form wire:submit="save" class="space-y-6">
                <!-- Name -->
                <flux:field>
                    <flux:label>Policy Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g. Exam Mode" required />
                    <flux:error name="name" />
                </flux:field>

                <!-- Allowed URLs -->
                <flux:field>
                    <flux:label>Allowed URLs</flux:label>
                    <flux:textarea wire:model="allowedUrlsInput" rows="3" placeholder="e.g. khanacademy.org, wikipedia.org" />
                    <flux:description>Enter domains separated by commas. Leave empty to allow all URLs (unless internet is blocked).</flux:description>
                    <flux:error name="allowedUrlsInput" />
                </flux:field>

                <!-- Blocked Keywords -->
                <flux:field>
                    <flux:label>Blocked Keywords</flux:label>
                    <flux:textarea wire:model="blockedKeywordsInput" rows="3" placeholder="e.g. answer, solution, cheat" />
                    <flux:description>Enter keywords separated by commas. Leave empty for no keyword restrictions.</flux:description>
                    <flux:error name="blockedKeywordsInput" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <!-- Internet Access -->
                    <flux:field>
                        <flux:checkbox wire:model="internetAccess" label="Allow internet access" />
                        <flux:description class="mt-1 ml-6">When disabled, students see a blocked message during sessions.</flux:description>
                        <flux:error name="internetAccess" />
                    </flux:field>

                    <!-- Default Policy -->
                    <flux:field>
                        <flux:checkbox wire:model="isDefault" label="Set as default policy for this classroom" />
                        <flux:description class="mt-1 ml-6">This policy will auto-apply when a new session starts.</flux:description>
                        <flux:error name="isDefault" />
                    </flux:field>
                </div>

                <div class="flex items-center space-x-3 pt-4">
                    <flux:button type="submit" variant="primary">Save Policy</flux:button>
                    <flux:button type="button" wire:click="cancelForm" variant="ghost">Cancel</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    <!-- Policy List -->
    <div class="grid grid-cols-1 gap-4">
        @forelse ($policies as $policy)
            <flux:card class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="space-y-3 flex-1">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ $policy->name }}</h3>
                        @if ($policy->is_default)
                            <flux:badge color="indigo" size="sm">Default</flux:badge>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <div class="flex items-center gap-2">
                            <flux:icon.globe-alt class="w-4 h-4" />
                            @if ($policy->internet_access)
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium">Internet allowed</span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-medium">Internet blocked</span>
                            @endif
                        </div>

                        <div class="mt-2">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">Allowed URLs:</span>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @if (empty($policy->allowed_urls))
                                    <span class="text-zinc-500 italic">No restrictions</span>
                                @else
                                    @foreach ($policy->allowed_urls as $url)
                                        <flux:badge color="zinc" size="sm">{{ $url }}</flux:badge>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="mt-2">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">Blocked Keywords:</span>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @if (empty($policy->blocked_keywords))
                                    <span class="text-zinc-500 italic">None</span>
                                @else
                                    @foreach ($policy->blocked_keywords as $keyword)
                                        <flux:badge color="amber" size="sm">{{ $keyword }}</flux:badge>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex sm:flex-col items-center sm:items-end gap-2 mt-4 sm:mt-0">
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="openEdit({{ $policy->id }})" size="sm" variant="ghost" icon="pencil">Edit</flux:button>
                        <flux:button wire:click="delete({{ $policy->id }})" wire:confirm="Are you sure you want to delete this policy?" size="sm" variant="danger" icon="trash">Delete</flux:button>
                    </div>
                    @if (!$policy->is_default)
                        <flux:button wire:click="setDefault({{ $policy->id }})" size="sm" variant="outline" class="w-full sm:w-auto mt-2 sm:mt-0">Set as Default</flux:button>
                    @endif
                </div>
            </flux:card>
        @empty
            @if (!$showForm)
                <div class="text-center py-12 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <flux:icon.shield-exclamation class="w-12 h-12 text-zinc-400 mx-auto mb-3" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">No policies found</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 mt-1 mb-4">Create your first policy to manage device access.</p>
                    <flux:button wire:click="openCreate" icon="plus" variant="primary">Create Policy</flux:button>
                </div>
            @endif
        @endforelse
    </div>
</div>
