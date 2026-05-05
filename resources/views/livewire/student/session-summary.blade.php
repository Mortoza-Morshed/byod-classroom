<div 
    x-data="{ 
        showModal: false, 
        activeResource: null,
        init() {
            setTimeout(() => $el.classList.remove('opacity-0'), 50);
        }
    }" 
    class="opacity-0 transition-opacity duration-700 ease-out space-y-8 pb-20"
>
    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. Header Section --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col items-center text-center py-8">
        <div @class([
            'w-20 h-20 rounded-full flex items-center justify-center mb-6 shadow-lg',
            'bg-emerald-500/20 border border-emerald-500/30' => $sessionDevice->violation_count === 0,
            'bg-amber-500/20 border border-amber-500/30' => $sessionDevice->violation_count > 0,
        ])>
            @if($sessionDevice->violation_count === 0)
                <flux:icon.check-circle class="w-10 h-10 text-emerald-400" variant="solid" />
            @else
                <flux:icon.exclamation-circle class="w-10 h-10 text-amber-400" variant="solid" />
            @endif
        </div>
        
        <h1 class="text-3xl font-bold text-[#ededed]">Session Complete</h1>
        <p class="mt-2 text-lg text-[#a1a1a1]">{{ $session->title }}</p>
        <p class="text-sm text-[#666666]">{{ $session->classroom->name }} &bull; Ended at {{ $session->ended_at?->format('g:i A') ?? 'N/A' }}</p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. Session Stats --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Time in Session --}}
        <div class="bg-[#111111] border border-[#2a2a2a] rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-sky-500/10 rounded-lg">
                    <flux:icon.clock class="w-5 h-5 text-sky-400" />
                </div>
                <span class="text-xs font-medium text-[#666666] uppercase tracking-wider">Time in Session</span>
            </div>
            <p class="text-2xl font-bold text-[#ededed]">{{ $timeInSession }}</p>
        </div>

        {{-- Resources Shared --}}
        <div class="bg-[#111111] border border-[#2a2a2a] rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-500/10 rounded-lg">
                    <flux:icon.document-text class="w-5 h-5 text-purple-400" />
                </div>
                <span class="text-xs font-medium text-[#666666] uppercase tracking-wider">Resources Shared</span>
            </div>
            <p class="text-2xl font-bold text-[#ededed]">{{ $session->resources->count() }}</p>
        </div>

        {{-- Violations --}}
        <div class="bg-[#111111] border border-[#2a2a2a] rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-red-500/10 rounded-lg">
                    <flux:icon.exclamation-triangle class="w-5 h-5 text-red-400" />
                </div>
                <span class="text-xs font-medium text-[#666666] uppercase tracking-wider">Your Violations</span>
            </div>
            <p @class([
                'text-2xl font-bold tabular-nums',
                'text-emerald-400' => $sessionDevice->violation_count === 0,
                'text-amber-400' => $sessionDevice->violation_count === 1,
                'text-red-400' => $sessionDevice->violation_count > 1,
            ])>{{ $sessionDevice->violation_count }}</p>
        </div>

        {{-- Focus Score --}}
        <div class="bg-[#111111] border border-[#2a2a2a] rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-{{ $focusScore['color'] }}-500/10 rounded-lg">
                    <flux:icon.star class="w-5 h-5 text-{{ $focusScore['color'] }}-400" />
                </div>
                <span class="text-xs font-medium text-[#666666] uppercase tracking-wider">Focus Score</span>
            </div>
            <p class="text-2xl font-bold text-{{ $focusScore['color'] }}-400">{{ $focusScore['label'] }}</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. Focus Summary Card --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-[#111111] border border-[#2a2a2a] rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#2a2a2a]">
            <h2 class="text-sm font-medium text-[#ededed] uppercase tracking-wider">Focus Breakdown</h2>
        </div>
        <div class="p-6">
            @if($sessionDevice->violation_count === 0)
                <div class="flex items-center gap-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-6">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-full flex items-center justify-center shrink-0">
                        <flux:icon.sparkles class="w-6 h-6 text-emerald-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-emerald-400">Great focus!</h3>
                        <p class="text-emerald-400/70 text-sm">You had no violations this session. Keep up the excellent work!</p>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    <div class="relative pl-6 border-l border-[#2a2a2a] ml-3 space-y-8">
                        @foreach($violations as $violation)
                            <div class="relative">
                                {{-- Timeline Dot --}}
                                <div class="absolute -left-[31px] top-1.5 w-3 h-3 rounded-full bg-red-500 border-2 border-[#111111]"></div>
                                
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-mono text-[#666666]">{{ $violation->created_at->format('g:i:s A') }}</span>
                                        <flux:badge size="sm" color="red" variant="subtle">Level {{ $violation->metadata['warning_level'] ?? 'N/A' }}</flux:badge>
                                    </div>
                                    <p class="text-sm text-[#ededed] font-medium">
                                        {{ match($violation->metadata['type'] ?? '') {
                                            'tab_switch' => 'Switched to another tab',
                                            'window_blur' => 'Switched to another application',
                                            'fullscreen_exit' => 'Exited fullscreen mode',
                                            default => $violation->description
                                        } }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="pt-6 border-t border-[#2a2a2a] flex items-start gap-3">
                        <flux:icon.information-circle class="w-5 h-5 text-[#666666] shrink-0 mt-0.5" />
                        <p class="text-xs text-[#666666] italic">
                            These violations were recorded by the system and are visible to your teacher. Repeated violations may result in disciplinary action or restricted access to future sessions.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 4. Session Resources --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="space-y-4">
        <h2 class="text-sm font-medium text-[#ededed] uppercase tracking-wider">Resources Shared</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($session->resources as $resource)
                <button 
                    type="button"
                    @click="
                        if ('{{ $resource->rendering_mode }}' === 'external') {
                            window.open('{{ $resource->accessUrl() }}', '_blank');
                        } else {
                            activeResource = @js($resource);
                            showModal = true;
                        }
                    "
                    class="flex items-center gap-4 bg-[#111111] border border-[#2a2a2a] rounded-xl p-4 text-left hover:border-sky-500/50 hover:bg-[#1a1a1a] transition-all group"
                >
                    <div class="w-10 h-10 bg-[#1a1a1a] rounded-lg flex items-center justify-center group-hover:bg-sky-500/10 transition-colors">
                        @if($resource->type === 'pdf')
                            <flux:icon.document-text class="w-5 h-5 text-red-400" />
                        @elseif($resource->type === 'website')
                            <flux:icon.globe-alt class="w-5 h-5 text-sky-400" />
                        @else
                            <flux:icon.document class="w-5 h-5 text-purple-400" />
                        @endif
                    </div>
                    
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-[#ededed] truncate group-hover:text-sky-400 transition-colors">{{ $resource->title }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] text-[#666666] uppercase tracking-wider">{{ $resource->type }}</span>
                            <span class="text-[10px] text-[#666666]">&bull; {{ $resource->created_at->format('g:i A') }}</span>
                        </div>
                    </div>
                    
                    <flux:icon.chevron-right class="w-4 h-4 text-[#666666] group-hover:text-sky-400 transition-colors" />
                </button>
            @empty
                <div class="col-span-full py-12 text-center bg-[#111111] border border-dashed border-[#2a2a2a] rounded-xl">
                    <p class="text-sm text-[#666666]">No resources were shared during this session.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 5. Announcements --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="space-y-4">
        <h2 class="text-sm font-medium text-[#ededed] uppercase tracking-wider">Session Announcements</h2>
        
        <div class="bg-[#111111] border border-[#2a2a2a] rounded-xl divide-y divide-[#2a2a2a]">
            @forelse($announcements as $announcement)
                <div class="p-4 flex gap-4">
                    <div class="w-8 h-8 bg-sky-500/10 rounded-full flex items-center justify-center shrink-0">
                        <flux:icon.megaphone class="w-4 h-4 text-sky-400" />
                    </div>
                    <div>
                        <p class="text-sm text-[#ededed] leading-relaxed">{{ $announcement->description }}</p>
                        <p class="text-[10px] text-[#666666] mt-1">{{ $announcement->created_at->format('g:i A') }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-[#666666]">No announcements were made this session.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 6. Action Buttons --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
        <a 
            href="{{ route('student.classrooms.show', $session->classroom_id) }}"
            class="w-full sm:w-auto px-8 py-3 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-xl transition-all shadow-lg shadow-sky-600/20 text-center"
        >
            Back to Classroom
        </a>
        <a 
            href="{{ route('student.dashboard') }}"
            class="w-full sm:w-auto px-8 py-3 bg-[#1a1a1a] border border-[#2a2a2a] hover:bg-[#2a2a2a] text-[#ededed] font-medium rounded-xl transition-all text-center"
        >
            Go to Dashboard
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Resource Viewer Modal --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div 
        x-show="showModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-10"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        {{-- Overlay --}}
        <div @click="showModal = false" class="absolute inset-0 bg-black/90 backdrop-blur-sm"></div>
        
        {{-- Modal Content --}}
        <div class="relative w-full max-w-6xl h-full bg-[#111111] border border-[#2a2a2a] rounded-2xl flex flex-col shadow-2xl overflow-hidden">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-[#2a2a2a] flex items-center justify-between bg-[#0a0a0a]/50">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-sky-500/10 rounded-lg flex items-center justify-center">
                        <template x-if="activeResource && activeResource.type === 'pdf'">
                            <flux:icon.document-text class="w-4 h-4 text-red-400" />
                        </template>
                        <template x-if="activeResource && activeResource.type === 'website'">
                            <flux:icon.globe-alt class="w-4 h-4 text-sky-400" />
                        </template>
                    </div>
                    <h2 class="text-sm font-semibold text-[#ededed]" x-text="activeResource ? activeResource.title : ''"></h2>
                </div>
                
                <button @click="showModal = false" class="p-2 hover:bg-[#2a2a2a] rounded-lg transition-colors text-[#666666] hover:text-[#ededed]">
                    <flux:icon.x-mark class="w-5 h-5" />
                </button>
            </div>
            
            {{-- Viewer Container --}}
            <div class="flex-1 bg-[#0a0a0a] relative overflow-hidden" wire:ignore>
                <template x-if="activeResource && activeResource.rendering_mode === 'pdfjs'">
                    <div class="w-full h-full flex flex-col bg-[#0f0f0f]">
                        <iframe :src="activeResource.access_url" class="w-full h-full border-0" type="application/pdf"></iframe>
                    </div>
                </template>
                
                <template x-if="activeResource && activeResource.rendering_mode === 'iframe'">
                    <iframe :src="activeResource.access_url" class="w-full h-full border-0 bg-white" sandbox="allow-scripts allow-same-origin allow-forms"></iframe>
                </template>
                
                <template x-if="!activeResource">
                    <div class="w-full h-full flex items-center justify-center">
                        <flux:icon.loading class="size-8 text-sky-400" />
                    </div>
                </template>
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-6 py-3 border-t border-[#2a2a2a] flex items-center justify-between bg-[#0a0a0a]/50">
                <span class="text-[10px] text-[#666666] uppercase tracking-widest font-mono" x-text="activeResource ? activeResource.type : ''"></span>
                <p class="text-[10px] text-[#666666]">End of Session Resource Viewer</p>
            </div>
        </div>
    </div>
</div>
