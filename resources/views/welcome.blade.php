<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#0a0a0a] text-[#ededed] min-h-screen flex flex-col antialiased selection:bg-blue-500/30 selection:text-white overflow-x-hidden">
        {{-- Decorative Glowing Orbs --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[500px] pointer-events-none overflow-hidden z-0">
            <div class="absolute top-[-250px] left-[15%] h-[500px] w-[500px] rounded-full bg-blue-500/10 blur-[120px]"></div>
            <div class="absolute top-[-200px] right-[15%] h-[500px] w-[500px] rounded-full bg-emerald-500/10 blur-[120px]"></div>
        </div>

        {{-- Top Navigation Bar --}}
        <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between border-b border-[#2a2a2a]/30">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-bold tracking-tight text-[#ededed]">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#111111] border border-[#2a2a2a] shadow-lg shadow-black/50">
                    <x-app-logo-icon class="h-6 fill-current text-blue-500" />
                </span>
                <span class="text-base tracking-tight font-extrabold">{{ config('app.name', 'BYOD Classroom') }}</span>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-[#ededed] bg-[#111111] border border-[#2a2a2a] hover:bg-[#1a1a1a] hover:border-[#3a3a3a] rounded-xl transition-all duration-200">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-[#a1a1a1] hover:text-[#ededed] transition-all duration-200">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-[#0a0a0a] bg-[#ededed] hover:bg-white rounded-xl transition-all duration-200 shadow-md">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        {{-- Main Hero Section --}}
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center max-w-5xl mx-auto px-6 py-12 lg:py-20 text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#111111] border border-[#2a2a2a] shadow-inner mb-6 lg:mb-8 animate-pulse">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold tracking-wider uppercase text-[#a1a1a1]">Active Sync Control Room</span>
            </div>

            {{-- Hero Title --}}
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight max-w-4xl bg-gradient-to-b from-[#ffffff] to-[#a1a1a1] bg-clip-text text-transparent">
                Bring Your Own Device.<br>Keep Your Classroom Focused.
            </h1>

            {{-- Subtitle --}}
            <p class="text-sm sm:text-base text-[#a1a1a1] max-w-2xl mt-6 leading-relaxed">
                BYOD Classroom provides real-time device monitoring, secure content locks, and live resource sharing to maximize teaching efficiency without hardware constraints.
            </p>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 mt-10 w-full sm:w-auto">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 text-xs font-bold uppercase tracking-widest text-[#0a0a0a] bg-[#ededed] hover:bg-white rounded-xl transition-all duration-200 shadow-xl flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Enter Teacher Portal
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 text-xs font-bold uppercase tracking-widest text-[#ededed] bg-[#111111] border border-[#2a2a2a] hover:bg-[#1a1a1a] hover:border-[#3a3a3a] rounded-xl transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-lg">
                    <svg class="w-4 h-4 text-[#a1a1a1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                    Join Session as Student
                </a>
            </div>

            {{-- Dashboard Preview Section with Vercel Style Glass Box --}}
            <div class="relative w-full max-w-4xl mt-16 sm:mt-24 p-1.5 rounded-2xl bg-gradient-to-b from-[#2a2a2a] to-transparent shadow-[0_0_50px_rgba(0,0,0,0.8)] overflow-hidden">
                <div class="bg-[#111111] rounded-xl border border-[#2a2a2a] overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-blue-500/50 to-transparent"></div>
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-[#2a2a2a] bg-[#0c0c0c]">
                        <div class="flex gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/30"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/30"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/30"></span>
                        </div>
                        <div class="mx-auto text-[10px] font-bold tracking-widest text-[#666666] uppercase">BYOD Session Instrument Control</div>
                    </div>
                    <div class="p-4 sm:p-8 grid grid-cols-1 sm:grid-cols-3 gap-4 bg-[#111111] text-left">
                        <div class="p-4 rounded-xl bg-[#0a0a0a] border border-[#2a2a2a] flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Connected Devices</span>
                            <span class="text-3xl font-extrabold text-[#ededed] tracking-tight">42 / 45</span>
                            <span class="text-[10px] text-emerald-400 font-bold">93% Active sync</span>
                        </div>
                        <div class="p-4 rounded-xl bg-[#0a0a0a] border border-[#2a2a2a] flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Active Policy</span>
                            <span class="text-3xl font-extrabold text-blue-400 tracking-tight">Locked Focus</span>
                            <span class="text-[10px] text-[#a1a1a1] font-bold">PDF Material restricted</span>
                        </div>
                        <div class="p-4 rounded-xl bg-[#0a0a0a] border border-[#2a2a2a] flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold text-[#666666] uppercase tracking-wider">Unresolved Violations</span>
                            <span class="text-3xl font-extrabold text-red-400 tracking-tight">0</span>
                            <span class="text-[10px] text-emerald-400 font-bold">All student devices secure</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-8 mt-12 border-t border-[#2a2a2a]/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#666666]">
            <div>&copy; {{ date('Y') }} {{ config('app.name', 'BYOD Classroom') }}. Built with Laravel + Livewire + Flux.</div>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-[#a1a1a1] transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-[#a1a1a1] transition-colors">Terms of Service</a>
            </div>
        </footer>
    </body>
</html>
