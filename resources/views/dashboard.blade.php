<x-app-layout>
    <div class="py-4 sm:py-8 max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

        <!-- iOS-style Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-[#86868b]">
                    <span>Overview</span>
                    <span>•</span>
                    <span>{{ now()->format('l, F j') }}</span>
                </div>
                <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-0.5">
                    Welcome back, {{ Auth::user()->name }}
                </h1>
            </div>

            <!-- Apple-style Quick Action Button -->
            <div>
                <a
                    href="{{ route('bucket') }}"
                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2 sm:py-2.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-xs sm:text-sm font-semibold transition-all shadow-sm active:scale-95 cursor-pointer"
                >
                    <i class="pi pi-box text-xs"></i>
                    <span>Open Myy Bucket</span>
                </a>
            </div>
        </div>

        <!-- Apple Widget Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">

            <!-- Widget 1: Storage Breakdown (Apple Health / iCloud style, Unlimited Storage) -->
            <div class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl rounded-[20px] sm:rounded-3xl p-4 sm:p-5 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center">
                                <i class="pi pi-cloud text-xs sm:text-sm"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Myy Bucket Storage</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/40 text-[#0071e3] dark:text-[#0a84ff]">
                            <i class="pi pi-infinity text-[10px]"></i>
                            <span>Unlimited</span>
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $formattedTotalBytes }}</span>
                            <span class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">of Unlimited Storage</span>
                        </div>

                        @if($totalSavedBytes > 0)
                            <div class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[10px] font-semibold">
                                <i class="pi pi-bolt text-[9px]"></i>
                                <span>Saved {{ $formattedSavedBytes }} ({{ $savingsPercent }}% space saved)</span>
                            </div>
                        @endif

                        <!-- Progress Bar (Dynamic category distribution) -->
                        <div class="w-full h-2 rounded-full bg-black/[0.04] dark:bg-white/[0.08] overflow-hidden flex mt-2.5">
                            @if($totalBytes > 0)
                                <div style="width: {{ max(4, $categories['image']['percentage']) }}%" class="bg-[#0071e3]" title="Images: {{ $categories['image']['percentage'] }}%"></div>
                                <div style="width: {{ $categories['video']['percentage'] }}%" class="bg-[#bf5af2]" title="Videos: {{ $categories['video']['percentage'] }}%"></div>
                                <div style="width: {{ $categories['audio']['percentage'] }}%" class="bg-[#ff9f0a]" title="Audio: {{ $categories['audio']['percentage'] }}%"></div>
                                <div style="width: {{ $categories['document']['percentage'] }}%" class="bg-[#30d158]" title="Docs: {{ $categories['document']['percentage'] }}%"></div>
                                <div style="width: {{ $categories['archive']['percentage'] + $categories['other']['percentage'] }}%" class="bg-gray-400" title="Others"></div>
                            @else
                                <div style="width: 100%" class="bg-gradient-to-r from-blue-500/20 via-indigo-500/20 to-purple-500/20"></div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-black/[0.04] dark:border-white/[0.06] grid grid-cols-2 gap-2 text-[10px] sm:text-[11px] text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-[#0071e3] shrink-0"></span>
                        <span class="truncate">Images: <strong>{{ $categories['image']['count'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-[#bf5af2] shrink-0"></span>
                        <span class="truncate">Videos: <strong>{{ $categories['video']['count'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-[#ff9f0a] shrink-0"></span>
                        <span class="truncate">Audio: <strong>{{ $categories['audio']['count'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-[#30d158] shrink-0"></span>
                        <span class="truncate">Docs: <strong>{{ $categories['document']['count'] }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Widget 2: Sync Status & Security -->
            <div class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl rounded-[20px] sm:rounded-3xl p-4 sm:p-5 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-[#30d158] flex items-center justify-center">
                                <i class="pi pi-check-circle text-xs sm:text-sm"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Cloud Status</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Online
                        </span>
                    </div>

                    <div class="mt-4 space-y-2 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-black/[0.03] dark:border-white/[0.04]">
                            <span class="text-gray-500 dark:text-gray-400">Total Files</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $totalFiles }} files</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-black/[0.03] dark:border-white/[0.04]">
                            <span class="text-gray-500 dark:text-gray-400">Folders</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $totalFolders }} folders</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-gray-500 dark:text-gray-400">Starred Files</span>
                            <span class="font-semibold text-amber-500 dark:text-amber-400">{{ $starredCount }} starred</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-2 text-[10px] text-gray-400">
                    Encrypted Cloud Storage • Automatic sync enabled
                </div>
            </div>

            <!-- Widget 3: Quick Navigation -->
            <div class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl rounded-[20px] sm:rounded-3xl p-4 sm:p-5 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between sm:col-span-2 lg:col-span-1">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-[#bf5af2] flex items-center justify-center">
                            <i class="pi pi-bolt text-xs sm:text-sm"></i>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Shortcuts</span>
                    </div>

                    <div class="space-y-1.5">
                        <a
                            href="{{ route('bucket') }}"
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.03] dark:hover:bg-white/[0.06] transition-colors group"
                        >
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-gray-200 font-medium">
                                <i class="pi pi-folder text-blue-500"></i>
                                <span>Browse Folders</span>
                            </div>
                            <i class="pi pi-chevron-right text-[10px] text-gray-400 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>

                        <a
                            href="{{ route('bucket') }}"
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.03] dark:hover:bg-white/[0.06] transition-colors group"
                        >
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-gray-200 font-medium">
                                <i class="pi pi-cloud-upload text-[#0071e3]"></i>
                                <span>Upload Files</span>
                            </div>
                            <i class="pi pi-chevron-right text-[10px] text-gray-400 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>

                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.03] dark:hover:bg-white/[0.06] transition-colors group"
                        >
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-gray-200 font-medium">
                                <i class="pi pi-user text-emerald-500"></i>
                                <span>Account Settings</span>
                            </div>
                            <i class="pi pi-chevron-right text-[10px] text-gray-400 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <div class="mt-3 text-[10px] text-gray-400">
                    Myy Bucket • Your Alternative to iCloud Cloud Storage
                </div>
            </div>

        </div>

        <!-- Recent Uploads Section -->
        <div class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center">
                        <i class="pi pi-history text-xs"></i>
                    </div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Recent Uploads</h2>
                </div>
                <a href="{{ route('bucket') }}" class="text-xs text-[#0071e3] dark:text-[#0a84ff] font-semibold hover:underline flex items-center gap-1">
                    <span>View all files</span>
                    <i class="pi pi-arrow-right text-[10px]"></i>
                </a>
            </div>

            @if($recentFiles->isEmpty())
                <div class="py-8 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center mx-auto mb-3">
                        <i class="pi pi-cloud-upload text-xl"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">No files uploaded yet</p>
                    <p class="text-[11px] text-[#86868b] mt-1 max-w-sm mx-auto">Upload documents, photos, audio, or videos into your unlimited cloud bucket.</p>
                    <a
                        href="{{ route('bucket') }}"
                        class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-xs font-semibold shadow-xs transition-all active:scale-95"
                    >
                        <i class="pi pi-upload text-xs"></i>
                        <span>Upload Your First File</span>
                    </a>
                </div>
            @else
                <div class="divide-y divide-black/[0.04] dark:divide-white/[0.06]">
                    @foreach($recentFiles as $recent)
                        <div class="py-2.5 sm:py-3 flex items-center justify-between gap-3 group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-black/[0.03] dark:bg-white/[0.06] flex items-center justify-center shrink-0">
                                    @if($recent->category === 'image')
                                        <i class="pi pi-image text-blue-500 text-sm"></i>
                                    @elseif($recent->category === 'video')
                                        <i class="pi pi-video text-purple-500 text-sm"></i>
                                    @elseif($recent->category === 'audio')
                                        <i class="pi pi-headphones text-amber-500 text-sm"></i>
                                    @elseif($recent->category === 'document')
                                        <i class="pi pi-file text-emerald-500 text-sm"></i>
                                    @else
                                        <i class="pi pi-box text-gray-500 text-sm"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $recent->name }}</p>
                                    <p class="text-[10px] sm:text-[11px] text-[#86868b]">{{ $recent->formatted_size }} • {{ $recent->formatted_updated_at }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a
                                    href="{{ route('bucket.file.download', $recent->id) }}"
                                    class="p-2 rounded-full hover:bg-black/[0.05] dark:hover:bg-white/[0.1] text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors"
                                    title="Download"
                                >
                                    <i class="pi pi-download text-xs"></i>
                                </a>
                                <a
                                    href="{{ route('bucket.file.preview', $recent->id) }}"
                                    target="_blank"
                                    class="p-2 rounded-full hover:bg-black/[0.05] dark:hover:bg-white/[0.1] text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors"
                                    title="Preview"
                                >
                                    <i class="pi pi-external-link text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>