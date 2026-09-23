<section>
    <header class="flex items-start gap-3 sm:gap-4 pb-4 border-b border-black/[0.05] dark:border-white/[0.08]">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
            <i class="pi pi-trash text-sm sm:text-base"></i>
        </div>
        <div>
            <h2 class="text-sm sm:text-base font-bold text-red-600 dark:text-red-400 tracking-tight">
                {{ __('Delete Account') }}
            </h2>
            <p class="mt-0.5 text-xs text-[#86868b]">
                {{ __('Permanently delete your Myy Bucket account, files, and all associated cloud storage data.') }}
            </p>
        </div>
    </header>

    <div class="mt-4 sm:mt-5 space-y-4">
        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-red-500/[0.06] border border-red-500/20 text-xs text-red-800 dark:text-red-300 flex items-start gap-3">
            <i class="pi pi-exclamation-triangle text-base text-red-500 shrink-0 mt-0.5"></i>
            <p class="leading-relaxed">
                {{ __('Once your account is deleted, all cloud files, folders, and multimedia stored in your bucket will be irreversibly wiped. Please download any files you want to keep before continuing.') }}
            </p>
        </div>

        <div>
            <x-danger-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            >
                <i class="pi pi-trash text-xs"></i>
                <span>{{ __('Delete Account') }}</span>
            </x-danger-button>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-7">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 pb-3 border-b border-black/[0.06] dark:border-white/[0.08]">
                <div class="w-9 h-9 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center shrink-0">
                    <i class="pi pi-exclamation-triangle text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>
                    <p class="text-xs text-[#86868b]">
                        {{ __('This action cannot be undone.') }}
                    </p>
                </div>
            </div>

            <p class="mt-4 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                {{ __('Please enter your account password to confirm that you want to permanently delete your Myy Bucket account and all stored multimedia files.') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <div class="relative">
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="ps-9"
                        placeholder="{{ __('Enter your password to confirm') }}"
                    />
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                        <i class="pi pi-lock text-xs"></i>
                    </div>
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button>
                    <i class="pi pi-trash text-xs"></i>
                    <span>{{ __('Permanently Delete') }}</span>
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
