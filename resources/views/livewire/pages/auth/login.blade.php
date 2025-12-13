<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
        <p class="text-gray-500 mt-2">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus
                autocomplete="username"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 outline-none"
                placeholder="name@company.com">
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:text-blue-700 font-medium" href="{{ route('password.request') }}"
                        wire:navigate>
                        Forgot password?
                    </a>
                @endif
            </div>
            <input wire:model="form.password" id="password" type="password" name="password" required
                autocomplete="current-password"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 outline-none"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="remember" class="ml-2 text-sm text-gray-600">
                Remember me for 30 days
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-100 transition-all duration-200 flex items-center justify-center gap-2">
            <span wire:loading.remove wire:target="login">Sign In</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Signing in...
            </span>
        </button>


    </form>
</div>