<div>
    {{-- Current device status --}}
    @if($existingDevice)
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Current Device</h2>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    {{-- Device type icon --}}
                    <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-xl flex items-center justify-center">
                        <flux:icon.computer-desktop class="w-6 h-6 text-zinc-600 dark:text-zinc-300" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $existingDevice->name }}
                        </p>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            {{ ucfirst($existingDevice->device_type) }}
                            @if($existingDevice->mac_address)
                                · {{ $existingDevice->mac_address }}
                            @endif
                        </p>
                        <p class="text-xs text-zinc-400 mt-0.5">
                            Registered {{ $existingDevice->registered_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Status badge --}}
                @if($existingDevice->status === 'approved')
                    <span class="flex items-center gap-1.5 text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-3 py-1.5 rounded-full font-medium">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        Approved
                    </span>
                @elseif($existingDevice->status === 'pending')
                    <span class="flex items-center gap-1.5 text-xs bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-3 py-1.5 rounded-full font-medium">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                        Pending Approval
                    </span>
                @else
                    <span class="flex items-center gap-1.5 text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 px-3 py-1.5 rounded-full font-medium">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        Blocked
                    </span>
                @endif
            </div>

            {{-- Status messages --}}
            @if($existingDevice->status === 'pending')
                <div class="mt-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                    <p class="text-xs text-amber-700 dark:text-amber-400">
                        Your device is waiting for approval from your teacher or admin.
                        You will be able to join sessions once approved.
                    </p>
                </div>
            @elseif($existingDevice->status === 'blocked')
                <div class="mt-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg p-3">
                    <p class="text-xs text-red-700 dark:text-red-400">
                        Your device has been blocked. Please contact your teacher or admin.
                    </p>
                </div>
            @endif
        </div>
    @endif

    {{-- Flash message --}}
    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 mb-6">
            <p class="text-sm text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Registration form --}}
    @if(!$existingDevice || $existingDevice->status === 'blocked')
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-1">
                Register {{ $existingDevice ? 'New' : 'Your' }} Device
            </h2>
            <p class="text-xs text-zinc-500 mb-5">
                Your device needs to be approved before you can join class sessions.
            </p>

            <form wire:submit="register" class="space-y-4">

                {{-- Device name --}}
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Device Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="name"
                        type="text"
                        placeholder="e.g. Rahul's HP Laptop"
                        class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                    />
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>



                {{-- MAC address (optional) --}}
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                        MAC Address <span class="text-zinc-400 font-normal">(optional)</span>
                    </label>
                    <input
                        wire:model="mac_address"
                        type="text"
                        placeholder="e.g. AA:BB:CC:DD:EE:FF"
                        class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition font-mono"
                    />
                    <p class="text-xs text-zinc-400 mt-1">
                        Found in your device's network settings. Helps identify your device on the school network.
                    </p>
                    @error('mac_address')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-sky-600 hover:bg-sky-700 disabled:opacity-60 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove>Register Device</span>
                    <span wire:loading>Registering...</span>
                </button>

            </form>
        </div>
    @endif
</div>
