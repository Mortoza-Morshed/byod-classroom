<?php
use Livewire\Volt\Volt;
?>
<x-layouts.app :title="$title ?? 'Admin'">

    <flux:sidebar sticky stashable class="bg-[#111111] border-r border-[#2a2a2a]">

        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <div class="flex items-center gap-3 px-4 py-5 border-b border-[#2a2a2a]">
            <div class="w-8 h-8 bg-blue-500/10 border border-blue-500/20 rounded-lg flex items-center justify-center shadow-[0_0_12px_rgba(59,130,246,0.15)]">
                <flux:icon.shield-check class="w-5 h-5 text-blue-400" />
            </div>
            <div>
                <p class="text-sm font-bold text-[#ededed]">BYOD Admin</p>
                <p class="text-xs text-[#a1a1a1]">{{ auth()->user()->name }}</p>
            </div>
        </div>

        <flux:navlist class="mt-4 px-2">
            <flux:navlist.item icon="squares-2x2" href="{{ route('admin.dashboard') }}" :current="request()->routeIs('admin.dashboard')">
                Dashboard
            </flux:navlist.item>

            <flux:navlist.group heading="Management" class="mt-4">
                <flux:navlist.item icon="users" href="{{ route('admin.users.index') }}" :current="request()->routeIs('admin.users.*')">
                    Users
                </flux:navlist.item>
                <flux:navlist.item icon="device-phone-mobile" href="{{ route('admin.devices.index') }}" :current="request()->routeIs('admin.devices.*')">
                    Devices
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Reports" class="mt-4">
                <flux:navlist.item icon="document-chart-bar" href="{{ route('admin.reports.index') }}" :current="request()->routeIs('admin.reports.*')">
                    Reports
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-list" href="{{ route('admin.logs.index') }}" :current="request()->routeIs('admin.logs.*')">
                    Audit Logs
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <div class="mt-auto px-4 py-4 border-t border-[#2a2a2a]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-sm text-[#a1a1a1] hover:text-[#ededed] transition-colors w-full font-medium">
                    <flux:icon.arrow-left-start-on-rectangle class="w-4 h-4 text-[#666666]" />
                    Logout
                </button>
            </form>
        </div>

    </flux:sidebar>

    <flux:main>
        <header class="sticky top-0 z-10 bg-[#0a0a0a]/80 backdrop-blur-md border-b border-[#2a2a2a] px-6 py-4 flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-3" />
                <h1 class="text-lg font-bold text-[#ededed]">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] bg-blue-500/10 border border-blue-500/20 text-blue-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-widest">Admin</span>
                <span class="text-sm font-semibold text-[#a1a1a1]">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <div class="px-6 pb-6">
            {{ $slot }}
        </div>
        
        <x-flash />
    </flux:main>

</x-layouts.app>