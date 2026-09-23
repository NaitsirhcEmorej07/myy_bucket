<x-app-layout>
    <div class="py-3 sm:py-6 max-w-4xl mx-auto px-3.5 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

        <!-- Apple Settings Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div
                    class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-[#86868b]">
                    <span>Apple ID</span>
                    <span>•</span>
                    <span>Account Settings</span>
                </div>
                <h1
                    class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex flex-wrap items-center gap-2 mt-0.5">
                    <span>{{ __('Myy Bucket Account') }}</span>
                    <span
                        class="text-[10px] sm:text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] dark:text-[#0a84ff]">
                        iCloud Alternative
                    </span>
                </h1>
            </div>

            <div>
                <a href="{{ route('bucket') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full border border-black/[0.08] dark:border-white/[0.12] bg-white/80 dark:bg-[#1c1c1e]/80 hover:bg-black/[0.04] dark:hover:bg-white/[0.08] text-gray-700 dark:text-gray-200 text-xs font-semibold backdrop-blur-xl transition-all active:scale-95 shadow-2xs">
                    <i class="pi pi-box text-xs text-[#0071e3] dark:text-[#0a84ff]"></i>
                    <span>Manage Bucket Files</span>
                </a>
            </div>
        </div>

        <!-- Apple ID Hero Banner Card -->
        <div
            class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card transition-all">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
                <!-- User Avatar Squircle -->
                <div class="relative shrink-0">
                    <div
                        class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 text-white flex items-center justify-center font-bold text-xl sm:text-2xl shadow-apple-card">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-[#30d158] border-2 border-white dark:border-[#1c1c1e] rounded-full shadow-2xs"
                        title="Online & Synced"></span>
                </div>

                <!-- User Info & Badges -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </h2>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-[#30d158]">
                            <i class="pi pi-verified text-[9px]"></i>
                            <span>Active</span>
                        </span>
                    </div>

                    <p class="text-xs text-[#86868b] truncate mt-0.5">
                        {{ Auth::user()->email }}
                    </p>

                    <!-- Meta Tags -->
                    <div class="flex flex-wrap items-center gap-2 mt-2.5 text-[11px] text-[#86868b]">
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-black/[0.03] dark:bg-white/[0.06] font-medium">
                            <i class="pi pi-calendar text-[10px]"></i>
                            <span>Member since
                                {{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : 'Recent' }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/40 text-[#0071e3] dark:text-[#0a84ff] font-medium">
                            <i class="pi pi-cloud text-[10px]"></i>
                            <span>Unlimited Storage - Cloud Tier</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Profile Information -->
        <div
            class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card transition-all">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Section 2: Security & Password -->
        <div
            class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card transition-all">
            @include('profile.partials.update-password-form')
        </div>

        <!-- Section 3: Delete Account (Danger Zone) -->
        <div
            class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-red-500/20 shadow-apple-card transition-all">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>