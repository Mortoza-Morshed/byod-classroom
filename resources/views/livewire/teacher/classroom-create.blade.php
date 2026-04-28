<div class="max-w-lg">
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">

        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-1">Classroom details</h2>
        <p class="text-xs text-zinc-500 mb-6">
            A unique join code will be generated automatically for students to enroll.
        </p>

        <form wire:submit="create" class="space-y-4">

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Classroom Name <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="name"
                    type="text"
                    placeholder="e.g. Class 10-A Computer Science"
                    class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                />
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="subject"
                    type="text"
                    placeholder="e.g. Computer Science"
                    class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                />
                @error('subject')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-sm font-medium py-2.5 px-5 rounded-lg transition-colors"
                >
                    <span wire:loading.remove>Create Classroom</span>
                    <span wire:loading>Creating...</span>
                </button>
                <a href="{{ route('teacher.classrooms.index') }}"
                   class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>