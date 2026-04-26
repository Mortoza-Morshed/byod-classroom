<?php
use Livewire\Volt\Volt;
?>
<x-layouts.app :title="$title ?? 'Admin'">

    <flux:sidebar sticky stashable class="bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700">

        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <div class="flex items-center gap-3 px-4 py-5 border-b border-zinc-200 dark:border-zinc-700">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <flux:icon.shield-check class="w-5 h-5 text-white" />
            </div>
            <div>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">BYOD Admin</p>
                <p class="text-xs text-zinc-500">{{ auth()->user()->name }}</p>
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

        <div class="mt-auto px-4 py-4 border-t border-zinc-200 dark:border-zinc-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors w-full">
                    <flux:icon.arrow-left-start-on-rectangle class="w-4 h-4" />
                    Logout
                </button>
            </form>
        </div>

    </flux:sidebar>

    <flux:main>
        <header class="sticky top-0 z-10 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-6 py-4 flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-3" />
                <h1 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full font-medium">Admin</span>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <div class="px-6 pb-6">
            {{ $slot }}
        </div>
    </flux:main>

</x-layouts.app>