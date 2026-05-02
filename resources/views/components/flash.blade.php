@if (session()->has('success') || session()->has('error') || session()->has('warning'))
    <div 
        x-data="{ show: true }" 
        x-init="setTimeout(() => show = false, 4000)" 
        x-show="show" 
        x-transition:leave="transition ease-in duration-300" 
        x-transition:leave-start="opacity-100 transform translate-y-0" 
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex flex-col gap-2"
    >
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-lg bg-emerald-600 px-4 py-3 text-white shadow-lg">
                <flux:icon.check-circle class="h-5 w-5" />
                <p class="text-sm font-medium">{{ session('success') }}</p>
                <button @click="show = false" class="ml-2 rounded-md hover:bg-emerald-700 p-1 transition">
                    <flux:icon.x-mark class="h-4 w-4" />
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 rounded-lg bg-red-600 px-4 py-3 text-white shadow-lg">
                <flux:icon.exclamation-circle class="h-5 w-5" />
                <p class="text-sm font-medium">{{ session('error') }}</p>
                <button @click="show = false" class="ml-2 rounded-md hover:bg-red-700 p-1 transition">
                    <flux:icon.x-mark class="h-4 w-4" />
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="flex items-center gap-3 rounded-lg bg-amber-500 px-4 py-3 text-white shadow-lg">
                <flux:icon.exclamation-triangle class="h-5 w-5" />
                <p class="text-sm font-medium">{{ session('warning') }}</p>
                <button @click="show = false" class="ml-2 rounded-md hover:bg-amber-600 p-1 transition">
                    <flux:icon.x-mark class="h-4 w-4" />
                </button>
            </div>
        @endif
    </div>
@endif

{{-- Alpine listener for Livewire dispatched events --}}
<div 
    x-data="{ show: false, message: '', type: 'success' }" 
    @notify.window="
        message = $event.detail.message; 
        type = $event.detail.type || 'success'; 
        show = true; 
        setTimeout(() => show = false, 4000)
    "
    x-show="show" 
    x-transition:leave="transition ease-in duration-300" 
    x-transition:leave-start="opacity-100 transform translate-y-0" 
    x-transition:leave-end="opacity-0 transform translate-y-2"
    style="display: none;"
    class="fixed bottom-6 right-6 z-50 flex flex-col gap-2"
>
    <div 
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-white shadow-lg"
        :class="{
            'bg-emerald-600': type === 'success',
            'bg-red-600': type === 'error',
            'bg-amber-500': type === 'warning'
        }"
    >
        <template x-if="type === 'success'">
            <flux:icon.check-circle class="h-5 w-5" />
        </template>
        <template x-if="type === 'error'">
            <flux:icon.exclamation-circle class="h-5 w-5" />
        </template>
        <template x-if="type === 'warning'">
            <flux:icon.exclamation-triangle class="h-5 w-5" />
        </template>
        
        <p class="text-sm font-medium" x-text="message"></p>
        
        <button @click="show = false" class="ml-2 rounded-md p-1 transition hover:bg-white/20">
            <flux:icon.x-mark class="h-4 w-4" />
        </button>
    </div>
</div>
