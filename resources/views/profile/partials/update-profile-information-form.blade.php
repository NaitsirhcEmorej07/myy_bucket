<section>
    <header class="flex items-start gap-3 sm:gap-4 pb-4 border-b border-black/[0.05] dark:border-white/[0.08]">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center shrink-0">
            <i class="pi pi-user text-sm sm:text-base"></i>
        </div>
        <div>
            <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">
                {{ __('Profile Information') }}
            </h2>
            <p class="mt-0.5 text-xs text-[#86868b]">
                {{ __("Update your account's display name and primary iCloud email address.") }}
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4 sm:mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Display Name')" />
            <div class="relative">
                <x-text-input id="name" name="name" type="text" class="ps-9" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                    <i class="pi pi-user text-xs"></i>
                </div>
            </div>
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="relative">
                <x-text-input id="email" name="email" type="email" class="ps-9" :value="old('email', $user->email)" required autocomplete="username" />
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                    <i class="pi pi-envelope text-xs"></i>
                </div>
            </div>
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-700 dark:text-amber-400">
                    <p class="flex items-center gap-1.5 font-medium">
                        <i class="pi pi-exclamation-circle"></i>
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button form="send-verification" class="mt-1.5 underline font-semibold hover:text-amber-900 dark:hover:text-amber-300">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-[#30d158]">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-2">
            <x-primary-button>
                <i class="pi pi-check text-xs"></i>
                <span>{{ __('Save Changes') }}</span>
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#30d158] bg-emerald-500/10 dark:bg-emerald-500/15 px-3 py-1.5 rounded-full"
                >
                    <i class="pi pi-check-circle text-xs"></i>
                    <span>{{ __('Saved successfully') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
