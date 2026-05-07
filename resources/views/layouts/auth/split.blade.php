<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#0a0a0a] text-[#ededed] antialiased">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            {{-- Left Panel --}}
            <div class="relative hidden h-full flex-col p-10 text-white lg:flex border-r border-[#2a2a2a] bg-gradient-to-br from-[#0a0a0a] via-[#111111] to-[#161616] overflow-hidden">
                {{-- Decorative grid overlay --}}
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#1f1f1f_1px,transparent_1px),linear-gradient(to_bottom,#1f1f1f_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-30"></div>
                <div class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-blue-500/5 blur-[150px]"></div>
                <div class="absolute -bottom-40 -right-40 h-[600px] w-[600px] rounded-full bg-emerald-500/5 blur-[150px]"></div>

                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-3 text-lg font-bold tracking-tight text-[#ededed]" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#111111] border border-[#2a2a2a] shadow-md">
                        <x-app-logo-icon class="h-6 fill-current text-[#ededed]" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto max-w-lg">
                    <blockquote class="space-y-4">
                        <p class="text-2xl font-bold tracking-tight leading-snug text-[#ededed]">&ldquo;{{ trim($message) }}&rdquo;</p>
                        <footer>
                            <p class="text-sm font-bold uppercase tracking-widest text-[#666666]">&mdash; {{ trim($author) }}</p>
                        </footer>
                    </blockquote>
                    <div class="mt-8 pt-8 border-t border-[#2a2a2a] flex items-center gap-4">
                        <div class="flex items-center gap-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 px-2.5 py-0.5 text-[10px] font-bold text-blue-400 uppercase tracking-widest">
                            Secure Focus
                        </div>
                        <div class="flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 text-[10px] font-bold text-emerald-400 uppercase tracking-widest">
                            Live Sync
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel --}}
            <div class="w-full lg:p-8 flex items-center justify-center min-h-screen bg-[#0a0a0a]">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[360px] p-6 sm:p-0">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden mb-6" wire:navigate>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#111111] border border-[#2a2a2a]">
                            <x-app-logo-icon class="h-6 fill-current text-[#ededed]" />
                        </span>
                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    <div class="bg-[#111111] border border-[#2a2a2a] rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-[#3a3a3a] to-transparent"></div>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
