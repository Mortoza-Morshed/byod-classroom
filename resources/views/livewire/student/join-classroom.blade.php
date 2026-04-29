<div class="max-w-md mx-auto mt-10">
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 bg-sky-100 dark:bg-sky-900/30 rounded-full mx-auto mb-4">
                <flux:icon.user-plus class="w-6 h-6 text-sky-600 dark:text-sky-400" />
            </div>
            
            <h2 class="text-xl font-semibold text-center text-zinc-900 dark:text-zinc-100 mb-2">Join a Classroom</h2>
            <p class="text-sm text-center text-zinc-500 dark:text-zinc-400 mb-6">Enter the 6-character code provided by your teacher.</p>

            <form wire:submit="join">
                <div class="mb-4">
                    <label for="joinCode" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Classroom Code</label>
                    <input 
                        wire:model="joinCode" 
                        type="text" 
                        id="joinCode"
                        class="w-full uppercase text-center tracking-widest text-lg font-mono px-4 py-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-600"
                        placeholder="ABCDEF"
                        maxlength="6"
                        required
                    />
                    @error('joinCode')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <flux:icon.exclamation-circle class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button 
                    type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg transition-colors font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-600 dark:focus:ring-offset-zinc-800 disabled:opacity-75"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="join">Join Classroom</span>
                    <span wire:loading wire:target="join" class="flex items-center gap-2">
                        <flux:icon.arrow-path class="w-4 h-4 animate-spin" />
                        Joining...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
