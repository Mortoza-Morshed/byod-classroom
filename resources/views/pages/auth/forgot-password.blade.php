<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-5">
        <x-auth-header :title="__('Reset Password')" :description="__('Enter your email to receive a password reset link')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
                class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
            />

            <div class="mt-2">
                <button type="submit" class="w-full bg-[#ededed] hover:bg-[#ffffff] text-[#0a0a0a] font-bold py-2.5 px-4 rounded-xl transition-all duration-200 uppercase tracking-widest text-xs shadow-lg flex items-center justify-center cursor-pointer" data-test="email-password-reset-link-button">
                    {{ __('Email reset link') }}
                </button>
            </div>
        </form>

        <div class="text-xs text-center text-[#a1a1a1] mt-2">
            <span>{{ __('Or, return to') }}</span>
            <a href="{{ route('login') }}" class="font-bold text-[#ededed] hover:underline" wire:navigate>{{ __('log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
