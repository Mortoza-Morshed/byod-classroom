<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-5">
        <x-auth-header :title="__('Welcome Back')" :description="__('Enter your credentials below to access your portal')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                    class="bg-[#1a1a1a] border-[#2a2a2a] text-[#ededed] placeholder-[#666666] focus:border-[#3a3a3a]"
                />

                @if (Route::has('password.request'))
                    <a class="absolute top-0 right-0 text-xs font-bold text-[#a1a1a1] hover:text-[#ededed] transition-colors" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember my session')" :checked="old('remember')" class="text-xs text-[#a1a1a1]" />

            <div class="mt-2">
                <button type="submit" class="w-full bg-[#ededed] hover:bg-[#ffffff] text-[#0a0a0a] font-bold py-2.5 px-4 rounded-xl transition-all duration-200 uppercase tracking-widest text-xs shadow-lg flex items-center justify-center cursor-pointer" data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="text-xs text-center text-[#a1a1a1] mt-2">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}" class="font-bold text-[#ededed] hover:underline" wire:navigate>{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
