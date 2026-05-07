<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-5">
        <x-auth-header :title="__('Create Account')" :description="__('Enter your details below to set up your profile')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
                class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
                class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
                class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
                class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
            />

            <div class="mt-2">
                <button type="submit" class="w-full bg-[#ededed] hover:bg-[#ffffff] text-[#0a0a0a] font-bold py-2.5 px-4 rounded-xl transition-all duration-200 uppercase tracking-widest text-xs shadow-lg flex items-center justify-center cursor-pointer" data-test="register-user-button">
                    {{ __('Create account') }}
                </button>
            </div>
        </form>

        <div class="text-xs text-center text-[#a1a1a1] mt-2">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="font-bold text-[#ededed] hover:underline" wire:navigate>{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
