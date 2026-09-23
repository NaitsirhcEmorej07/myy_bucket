<section>
    <header class="flex items-start gap-3 sm:gap-4 pb-4 border-b border-black/[0.05] dark:border-white/[0.08]">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <i class="pi pi-lock text-sm sm:text-base"></i>
        </div>
        <div>
            <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">
                {{ __('Update Password') }}
            </h2>
            <p class="mt-0.5 text-xs text-[#86868b]">
                {{ __('Ensure your account is using a strong, unique password to keep your bucket files protected.') }}
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4 sm:mt-5 space-y-4">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="relative">
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="ps-9" autocomplete="current-password" />
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                    <i class="pi pi-key text-xs"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="relative">
                <x-text-input id="update_password_password" name="password" type="password" class="ps-9" autocomplete="new-password" />
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                    <i class="pi pi-lock text-xs"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm New Password')" />
            <div class="relative">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="ps-9" autocomplete="new-password" />
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                    <i class="pi pi-lock text-xs"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-3 pt-2">
            <x-primary-button>
                <i class="pi pi-shield text-xs"></i>
                <span>{{ __('Update Password') }}</span>
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#30d158] bg-emerald-500/10 dark:bg-emerald-500/15 px-3 py-1.5 rounded-full"
                >
                    <i class="pi pi-check-circle text-xs"></i>
                    <span>{{ __('Password updated') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
