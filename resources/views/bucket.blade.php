<x-app-layout>
    <div x-data="bucketManager()" x-init="init()" class="py-3 sm:py-6 max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-3.5 sm:space-y-6">

        <!-- Apple-style Top Bar & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-[#86868b]">
                    <span>iCloud Drive</span>
                    <span>•</span>
                    <span x-text="currentFolder ? currentFolder.name : 'All Files'"></span>
                </div>
                <h1 class="text-lg sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex flex-wrap items-center gap-2 mt-0.5">
                    <span>Myy Bucket</span>
                    <span class="text-[10px] sm:text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] dark:text-[#0a84ff]">
                        Your Alternative to iCloud Cloud Storage
                    </span>
                </h1>
            </div>

            <!-- Hidden file input for uploads (used inside folders) -->
            <input
                type="file"
                x-ref="fileInput"
                @change="handleFileUpload($event)"
                multiple
                class="hidden"
            />
        </div>

        <!-- iOS Search Bar & Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <!-- iOS-style Pill Search Input -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="pi pi-search text-xs"></i>
                </span>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search in Bucket..."
                    class="w-full pl-8 pr-8 py-1.5 sm:py-2 text-xs rounded-full border-none bg-black/[0.05] dark:bg-white/[0.08] text-gray-900 dark:text-white placeholder-[#86868b] focus:ring-2 focus:ring-[#0071e3] focus:outline-none transition-all"
                />
                <button
                    x-show="searchQuery.length > 0"
                    @click="searchQuery = ''"
                    type="button"
                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                >
                    <i class="pi pi-times-circle text-xs"></i>
                </button>
            </div>

            <!-- View Switcher & Counter -->
            <div class="flex items-center justify-between sm:justify-end gap-2 text-xs">
                <span class="text-[11px] text-[#86868b] sm:hidden" x-text="filteredFiles().length + ' files'"></span>

                <div class="flex items-center p-0.5 bg-black/[0.04] dark:bg-white/[0.06] rounded-full">
                    <button
                        @click="viewMode = 'grid'"
                        type="button"
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-[#2c2c2e] text-[#0071e3] dark:text-[#0a84ff] shadow-xs font-bold' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                        class="px-2.5 py-1 rounded-full text-xs transition-all cursor-pointer flex items-center gap-1"
                        title="Grid View"
                    >
                        <i class="pi pi-th-large text-xs"></i>
                        <span class="text-[10px] hidden sm:inline-block">Grid</span>
                    </button>
                    <button
                        @click="viewMode = 'list'"
                        type="button"
                        :class="viewMode === 'list' ? 'bg-white dark:bg-[#2c2c2e] text-[#0071e3] dark:text-[#0a84ff] shadow-xs font-bold' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                        class="px-2.5 py-1 rounded-full text-xs transition-all cursor-pointer flex items-center gap-1"
                        title="List View"
                    >
                        <i class="pi pi-list text-xs"></i>
                        <span class="text-[10px] hidden sm:inline-block">List</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Apple-style Segmented Filter Bar -->
        <div class="flex items-center gap-1 overflow-x-auto pb-1.5 scrollbar-none text-[11px] sm:text-xs">
            <button
                @click="activeFilter = 'all'"
                type="button"
                :class="activeFilter === 'all' ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-objects-column text-[10px]"></i>
                <span>All</span>
                <span class="opacity-75 text-[10px]" x-text="files.length + (currentFolder ? 0 : folders.length)"></span>
            </button>

            <button
                x-show="!currentFolder"
                @click="activeFilter = 'folders'"
                type="button"
                :class="activeFilter === 'folders' ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-folder text-[10px]"></i>
                <span>Folders</span>
                <span class="opacity-75 text-[10px]" x-text="folders.length"></span>
            </button>

            <button
                @click="activeFilter = 'image'"
                type="button"
                :class="activeFilter === 'image' ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-image text-[10px] text-sky-500"></i>
                <span>Photos</span>
                <span class="opacity-75 text-[10px]" x-text="countFilesByType('image')"></span>
            </button>

            <button
                @click="activeFilter = 'video'"
                type="button"
                :class="activeFilter === 'video' ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-video text-[10px] text-purple-500"></i>
                <span>Videos</span>
                <span class="opacity-75 text-[10px]" x-text="countFilesByType('video')"></span>
            </button>

            <button
                @click="activeFilter = 'audio'"
                type="button"
                :class="activeFilter === 'audio' ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-volume-up text-[10px] text-emerald-500"></i>
                <span>Audio</span>
                <span class="opacity-75 text-[10px]" x-text="countFilesByType('audio')"></span>
            </button>

            <button
                @click="activeFilter = 'document'"
                type="button"
                :class="activeFilter === 'document' ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-file text-[10px] text-amber-500"></i>
                <span>Docs</span>
                <span class="opacity-75 text-[10px]" x-text="countFilesByType('document')"></span>
            </button>

            <button
                @click="activeFilter = 'starred'"
                type="button"
                :class="activeFilter === 'starred' ? 'bg-amber-500 text-white font-semibold shadow-xs' : 'bg-black/[0.04] dark:bg-white/[0.06] text-gray-600 dark:text-gray-300 hover:bg-black/[0.08] dark:hover:bg-white/[0.1]'"
                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full whitespace-nowrap transition-all cursor-pointer flex items-center gap-1.5"
            >
                <i class="pi pi-star-fill text-[10px] text-amber-400"></i>
                <span>Favorites</span>
                <span class="opacity-75 text-[10px]" x-text="countStarredFiles()"></span>
            </button>
        </div>

        <!-- Breadcrumbs Navigation -->
        <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
            <button
                @click="navigateToRoot()"
                type="button"
                class="hover:text-[#0071e3] dark:hover:text-[#0a84ff] font-medium flex items-center gap-1 transition-colors cursor-pointer"
            >
                <i class="pi pi-home text-[10px]"></i>
                <span>iCloud</span>
            </button>
            <template x-if="currentFolder">
                <div class="flex items-center gap-1.5">
                    <i class="pi pi-chevron-right text-[8px] text-gray-400"></i>
                    <span class="font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-1">
                        <i class="pi pi-folder text-[#0071e3]"></i>
                        <span x-text="currentFolder.name"></span>
                    </span>
                    <button
                        @click="navigateToRoot()"
                        type="button"
                        class="ms-2 px-2 py-0.5 rounded-full text-[10px] bg-black/[0.04] dark:bg-white/[0.08] hover:bg-black/[0.08] text-gray-700 dark:text-gray-300 transition-colors"
                    >
                        Back
                    </button>
                </div>
            </template>
        </nav>

        <!-- SECTION: Folders (Apple squircle cards) -->
        <div x-show="!currentFolder && (activeFilter === 'all' || activeFilter === 'folders')" class="space-y-2">
            <h2 class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-[#86868b]">
                Folders (<span x-text="filteredFolders().length"></span>)
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3.5">
                <!-- Create Folder Card -->
                <button
                    @click="openNewFolderModal()"
                    type="button"
                    class="group relative bg-white/50 dark:bg-[#1c1c1e]/50 hover:bg-white dark:hover:bg-[#1c1c1e] rounded-[18px] sm:rounded-2xl p-2.5 sm:p-3.5 border-2 border-dashed border-gray-300 dark:border-white/20 hover:border-[#0071e3] dark:hover:border-[#0a84ff] shadow-xs hover:shadow-apple-card transition-all duration-200 cursor-pointer flex flex-col justify-between text-left min-h-[105px] sm:min-h-[120px]"
                >
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center transition-transform group-hover:scale-110 shadow-2xs">
                        <i class="pi pi-folder-plus text-sm sm:text-base"></i>
                    </div>

                    <div class="mt-2.5">
                        <h3 class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white group-hover:text-[#0071e3] dark:group-hover:text-[#0a84ff] transition-colors flex items-center gap-1">
                            <span>Create folder</span>
                        </h3>
                        <div class="text-[10px] text-[#86868b] mt-0.5">
                            New directory
                        </div>
                    </div>
                </button>

                <template x-for="folder in filteredFolders()" :key="folder.id">
                    <div
                        @click="openFolder(folder)"
                        class="group relative bg-white/80 dark:bg-[#1c1c1e]/80 rounded-[18px] sm:rounded-2xl p-2.5 sm:p-3.5 border border-black/[0.05] dark:border-white/[0.07] hover:border-[#0071e3]/40 shadow-apple-card hover:shadow-apple-float transition-all duration-200 cursor-pointer flex flex-col justify-between"
                    >
                        <div class="flex items-start justify-between">
                            <div
                                :class="getFolderBgClass(folder.color)"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105"
                            >
                                <i :class="getFolderIconClass(folder.color)" class="pi pi-folder text-sm sm:text-base"></i>
                            </div>

                            <!-- Options Dropdown -->
                            <div class="relative" @click.stop>
                                <button
                                    @click="toggleFolderMenu(folder.id)"
                                    type="button"
                                    class="p-1 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-black/[0.04] dark:hover:bg-white/[0.06]"
                                >
                                    <i class="pi pi-ellipsis-h text-xs"></i>
                                </button>
                                <div
                                    x-show="openMenuFolderId === folder.id"
                                    @click.outside="openMenuFolderId = null"
                                    x-cloak
                                    class="absolute right-0 mt-1 w-32 bg-white/95 dark:bg-[#2c2c2e]/95 backdrop-blur-xl border border-black/[0.08] dark:border-white/[0.1] rounded-2xl shadow-apple-float z-30 py-1 text-xs"
                                >
                                    <button
                                        @click="openRenameModal(folder, true); openMenuFolderId = null"
                                        class="w-full text-left px-3 py-1.5 hover:bg-black/[0.04] dark:hover:bg-white/[0.06] flex items-center gap-2 text-gray-700 dark:text-gray-300"
                                    >
                                        <i class="pi pi-pencil text-[10px]"></i> Rename
                                    </button>
                                    <button
                                        @click="deleteFolder(folder.id); openMenuFolderId = null"
                                        class="w-full text-left px-3 py-1.5 hover:bg-red-50 dark:hover:bg-red-950/30 text-red-600 dark:text-red-400 flex items-center gap-2"
                                    >
                                        <i class="pi pi-trash text-[10px]"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2.5">
                            <h3 class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white truncate group-hover:text-[#0071e3] transition-colors" x-text="folder.name"></h3>
                            <div class="flex items-center justify-between text-[10px] text-[#86868b] mt-0.5">
                                <span x-text="getFolderFileCount(folder.id) + ' items'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- SECTION: Active Folder Header & Upload Area (Only when inside a folder) -->
        <div x-show="currentFolder" class="space-y-3">
            <div class="bg-white/80 dark:bg-[#1c1c1e]/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-black/[0.05] dark:border-white/[0.07] shadow-apple-card flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        @click="navigateToRoot()"
                        type="button"
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-black/[0.04] dark:bg-white/[0.08] hover:bg-black/[0.08] text-gray-700 dark:text-gray-300 flex items-center justify-center transition-colors cursor-pointer shrink-0"
                        title="Back to All Folders"
                    >
                        <i class="pi pi-arrow-left text-xs"></i>
                    </button>
                    <div
                        :class="getFolderBgClass(currentFolder?.color)"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0"
                    >
                        <i :class="getFolderIconClass(currentFolder?.color)" class="pi pi-folder text-base sm:text-lg"></i>
                    </div>
                    <div class="truncate">
                        <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white truncate" x-text="currentFolder?.name"></h2>
                        <p class="text-[10px] sm:text-[11px] text-[#86868b] mt-0.5">
                            <span x-text="filteredFiles().length + ' file' + (filteredFiles().length === 1 ? '' : 's')"></span>
                            <span class="hidden sm:inline">• Tap Upload or drag files below</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        @click="$refs.fileInput.click()"
                        type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold bg-[#0071e3] hover:bg-[#0077ed] text-white shadow-xs hover:shadow-apple-float transition-all active:scale-95 cursor-pointer"
                    >
                        <i class="pi pi-cloud-upload text-sm"></i>
                        <span>Upload Files</span>
                    </button>
                </div>
            </div>

            <!-- Folder Dropzone -->
            <div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop($event)"
                @click="$refs.fileInput.click()"
                :class="isDragging ? 'border-[#0071e3] bg-blue-50/50 dark:bg-blue-950/20 ring-2 ring-[#0071e3]/20 scale-[1.005]' : 'border-black/[0.08] dark:border-white/[0.1] bg-white/40 dark:bg-[#1c1c1e]/40 hover:bg-white/80 dark:hover:bg-[#1c1c1e]/80'"
                class="hidden sm:block border-2 border-dashed rounded-2xl p-4 sm:p-5 text-center cursor-pointer transition-all duration-200"
            >
                <div class="flex items-center justify-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] flex items-center justify-center shrink-0">
                        <i class="pi pi-cloud-upload text-sm"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">
                            Tap to upload or drag files into <span class="text-[#0071e3] dark:text-[#0a84ff]" x-text="currentFolder?.name"></span>
                        </p>
                        <p class="text-[10px] text-[#86868b]">Supports photos, videos, audio, and documents (up to 512MB)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: Files (Grid Mode) -->
        <div x-show="viewMode === 'grid'" class="space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-[#86868b]">
                    Files (<span x-text="filteredFiles().length"></span>)
                </h2>
                <span class="text-[10px] text-[#86868b] hidden sm:inline" x-show="filteredFiles().length > 0">
                    Showing <span x-text="filteredFiles().length"></span> items
                </span>
            </div>

            <!-- Apple-style Compact File Grid -->
            <div x-show="filteredFiles().length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5 sm:gap-4">
                <template x-for="file in filteredFiles()" :key="file.id">
                    <div class="group relative bg-white/80 dark:bg-[#1c1c1e]/80 rounded-[18px] sm:rounded-2xl border border-black/[0.05] dark:border-white/[0.07] hover:border-[#0071e3]/40 shadow-apple-card hover:shadow-apple-float transition-all duration-200 overflow-hidden flex flex-col justify-between">

                        <!-- Thumbnail Area (Compact on mobile) -->
                        <div
                            @click="openPreview(file)"
                            class="relative h-24 sm:h-36 bg-black/[0.02] dark:bg-white/[0.02] flex items-center justify-center overflow-hidden cursor-pointer"
                        >
                            <!-- Image Preview -->
                            <template x-if="file.type === 'image'">
                                <img
                                    :src="file.url"
                                    :alt="file.name"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                />
                            </template>

                            <!-- Video Placeholder -->
                            <template x-if="file.type === 'video'">
                                <div class="flex flex-col items-center justify-center text-[#bf5af2]">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/50 flex items-center justify-center transition-transform group-hover:scale-105 shadow-2xs">
                                        <i class="pi pi-video text-base sm:text-xl"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1 text-[#bf5af2]">Video</span>
                                </div>
                            </template>

                            <!-- Audio Placeholder -->
                            <template x-if="file.type === 'audio'">
                                <div class="flex flex-col items-center justify-center text-[#30d158]">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center transition-transform group-hover:scale-105 shadow-2xs">
                                        <i class="pi pi-volume-up text-base sm:text-xl"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1 text-[#30d158]">Audio</span>
                                </div>
                            </template>

                            <!-- Document Placeholder -->
                            <template x-if="file.type === 'document'">
                                <div class="flex flex-col items-center justify-center text-[#ff9f0a]">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center transition-transform group-hover:scale-105 shadow-2xs">
                                        <i :class="getDocumentIcon(file.extension)" class="pi text-base sm:text-xl"></i>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1 text-[#ff9f0a]" x-text="file.extension"></span>
                                </div>
                            </template>

                            <!-- Star Toggle -->
                            <button
                                @click.stop="toggleStar(file)"
                                type="button"
                                class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 p-1 sm:p-1.5 rounded-full bg-white/80 dark:bg-black/60 backdrop-blur-md transition-transform active:scale-90"
                                :class="file.starred ? 'text-amber-400' : 'text-gray-400 hover:text-amber-400'"
                                title="Favorite"
                            >
                                <i :class="file.starred ? 'pi pi-star-fill' : 'pi pi-star'" class="text-[10px] sm:text-xs"></i>
                            </button>

                            <!-- Extension pill -->
                            <span class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-bold uppercase bg-black/60 text-white backdrop-blur-md" x-text="file.extension"></span>
                        </div>

                        <!-- Card Body (Compact on mobile) -->
                        <div class="p-2 sm:p-3 flex flex-col justify-between flex-1">
                            <div>
                                <h3
                                    @click="openPreview(file)"
                                    class="text-[11px] sm:text-xs font-semibold text-gray-900 dark:text-white truncate hover:text-[#0071e3] transition-colors cursor-pointer"
                                    :title="file.name"
                                    x-text="file.name"
                                ></h3>
                                <div class="flex items-center justify-between text-[9px] sm:text-[10px] text-[#86868b] mt-0.5">
                                    <div class="flex items-center gap-1">
                                        <span x-text="file.size"></span>
                                        <template x-if="file.savingsPercent > 0">
                                            <span class="inline-flex items-center gap-0.5 px-1 py-0.2 rounded text-[8px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400" :title="'Saved ' + file.savingsPercent + '% space in bucket'">
                                                <i class="pi pi-bolt text-[7px]"></i>
                                                <span x-text="file.savingsPercent + '%'"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <span x-text="file.updatedAt"></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-2 mt-1.5 border-t border-black/[0.04] dark:border-white/[0.06]">
                                <button
                                    @click="openPreview(file)"
                                    type="button"
                                    class="text-[10px] text-[#0071e3] font-semibold hover:underline cursor-pointer"
                                >
                                    Quick Look
                                </button>

                                <div class="flex items-center gap-0.5 sm:gap-1">
                                    <button
                                        @click="downloadFile(file)"
                                        type="button"
                                        class="p-1 rounded-full text-gray-500 hover:text-[#0071e3] hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors cursor-pointer"
                                        title="Download"
                                    >
                                        <i class="pi pi-download text-[10px]"></i>
                                    </button>
                                    <button
                                        @click="openRenameModal(file, false)"
                                        type="button"
                                        class="p-1 rounded-full text-gray-500 hover:text-amber-500 hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors cursor-pointer"
                                        title="Rename"
                                    >
                                        <i class="pi pi-pencil text-[10px]"></i>
                                    </button>
                                    <button
                                        @click="deleteFile(file.id)"
                                        type="button"
                                        class="p-1 rounded-full text-gray-500 hover:text-red-500 hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors cursor-pointer"
                                        title="Delete"
                                    >
                                        <i class="pi pi-trash text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>

        <!-- SECTION: Files (List Mode) -->
        <div x-show="viewMode === 'list'" class="space-y-2">
            <div class="bg-white/80 dark:bg-[#1c1c1e]/80 rounded-[18px] sm:rounded-2xl border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[#86868b] border-b border-black/[0.05] dark:border-white/[0.06]">
                            <tr>
                                <th class="py-2.5 px-3 font-semibold text-[11px]">Name</th>
                                <th class="py-2.5 px-3 font-semibold text-[11px] hidden sm:table-cell">Type</th>
                                <th class="py-2.5 px-3 font-semibold text-[11px]">Size</th>
                                <th class="py-2.5 px-3 font-semibold text-[11px] hidden md:table-cell">Date</th>
                                <th class="py-2.5 px-3 font-semibold text-[11px] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/[0.03] dark:divide-white/[0.04]">
                            <template x-for="file in filteredFiles()" :key="file.id">
                                <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                    <td class="py-2.5 px-3 flex items-center gap-2.5">
                                        <div
                                            @click="openPreview(file)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center cursor-pointer shrink-0"
                                            :class="getFileIconBadgeBg(file.type)"
                                        >
                                            <i :class="getFileIconClass(file.type, file.extension)" class="pi text-xs"></i>
                                        </div>
                                        <div class="truncate max-w-[130px] sm:max-w-xs">
                                            <p
                                                @click="openPreview(file)"
                                                class="font-semibold text-xs text-gray-900 dark:text-white truncate hover:text-[#0071e3] cursor-pointer"
                                                x-text="file.name"
                                            ></p>
                                        </div>
                                        <button
                                            @click="toggleStar(file)"
                                            type="button"
                                            class="ms-1"
                                            :class="file.starred ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600'"
                                        >
                                            <i :class="file.starred ? 'pi pi-star-fill' : 'pi pi-star'" class="text-[10px]"></i>
                                        </button>
                                    </td>
                                    <td class="py-2.5 px-3 hidden sm:table-cell">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-black/[0.04] dark:bg-white/[0.08] text-gray-600 dark:text-gray-300" x-text="file.extension"></span>
                                    </td>
                                    <td class="py-2.5 px-3 text-[11px] text-[#86868b] font-mono">
                                        <div class="flex items-center gap-1.5">
                                            <span x-text="file.size"></span>
                                            <template x-if="file.savingsPercent > 0">
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                                    <i class="pi pi-bolt text-[7px]"></i>
                                                    <span x-text="file.savingsPercent + '% saved'"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-[11px] text-[#86868b] hidden md:table-cell" x-text="file.updatedAt"></td>
                                    <td class="py-2.5 px-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <button
                                                @click="openPreview(file)"
                                                type="button"
                                                class="p-1 rounded-full text-gray-500 hover:text-[#0071e3]"
                                                title="View"
                                            >
                                                <i class="pi pi-eye text-xs"></i>
                                            </button>
                                            <button
                                                @click="downloadFile(file)"
                                                type="button"
                                                class="p-1 rounded-full text-gray-500 hover:text-[#0071e3]"
                                                title="Download"
                                            >
                                                <i class="pi pi-download text-xs"></i>
                                            </button>
                                            <button
                                                @click="openRenameModal(file, false)"
                                                type="button"
                                                class="p-1 rounded-full text-gray-500 hover:text-amber-500"
                                                title="Rename"
                                            >
                                                <i class="pi pi-pencil text-xs"></i>
                                            </button>
                                            <button
                                                @click="deleteFile(file.id)"
                                                type="button"
                                                class="p-1 rounded-full text-gray-500 hover:text-red-500"
                                                title="Delete"
                                            >
                                                <i class="pi pi-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty State (When folder is empty) -->
        <div
            x-show="currentFolder && filteredFiles().length === 0"
            class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-8 sm:p-12 text-center border border-black/[0.05] dark:border-white/[0.07] shadow-apple-card"
        >
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center mx-auto mb-3 shadow-2xs">
                <i class="pi pi-inbox text-2xl sm:text-3xl"></i>
            </div>
            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">This folder is empty</h3>
            <p class="text-[11px] sm:text-xs text-[#86868b] mt-1 max-w-xs mx-auto">
                No files uploaded yet in <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="currentFolder?.name"></span>.
            </p>
            <div class="mt-4 flex items-center justify-center gap-2">
                <button
                    @click="$refs.fileInput.click()"
                    type="button"
                    class="px-4 py-1.5 sm:py-2 rounded-full text-xs font-semibold bg-[#0071e3] hover:bg-[#0077ed] text-white cursor-pointer shadow-xs active:scale-95"
                >
                    <i class="pi pi-cloud-upload me-1"></i> Upload Files
                </button>
            </div>
        </div>

        <!-- Empty State (When filter yields 0 results) -->
        <div
            x-show="!currentFolder && activeFilter !== 'all' && activeFilter !== 'folders' && filteredFiles().length === 0"
            class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-8 sm:p-12 text-center border border-black/[0.05] dark:border-white/[0.07] shadow-apple-card"
        >
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-400 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                <i class="pi pi-filter-slash text-2xl sm:text-3xl"></i>
            </div>
            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">No files found</h3>
            <p class="text-[11px] sm:text-xs text-[#86868b] mt-1 max-w-xs mx-auto">
                No matching files found for this category filter.
            </p>
        </div>

        <!-- MODAL: Apple-style Quick Look Preview Sheet -->
        <div
            x-show="previewModal.open"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-3 sm:p-4"
        >
            <!-- Backdrop -->
            <div
                x-show="previewModal.open"
                @click="closePreview()"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-md"
            ></div>

            <!-- Dialog Content -->
            <div
                x-show="previewModal.open"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-[#1c1c1e] rounded-2xl sm:rounded-3xl max-w-xl w-full border border-black/[0.08] dark:border-white/[0.12] shadow-2xl overflow-hidden"
            >
                <!-- Sheet Header -->
                <div class="flex items-center justify-between p-3.5 sm:p-4 border-b border-black/[0.06] dark:border-white/[0.08]">
                    <div class="flex items-center gap-2 min-w-0">
                        <div
                            class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                            :class="previewModal.file ? getFileIconBadgeBg(previewModal.file.type) : ''"
                        >
                            <i :class="previewModal.file ? getFileIconClass(previewModal.file.type, previewModal.file.extension) : ''" class="pi text-xs"></i>
                        </div>
                        <div class="truncate">
                            <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate" x-text="previewModal.file?.name"></h3>
                            <p class="text-[10px] text-[#86868b] flex items-center gap-1.5 flex-wrap mt-0.5">
                                <span x-text="(previewModal.file?.size || '') + ' • ' + (previewModal.file?.updatedAt || '')"></span>
                                <template x-if="previewModal.file?.savingsPercent > 0">
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                        <i class="pi pi-bolt text-[7px]"></i>
                                        <span x-text="previewModal.file.savingsPercent + '% space saved in bucket'"></span>
                                    </span>
                                </template>
                            </p>
                        </div>
                    </div>
                    <button
                        @click="closePreview()"
                        type="button"
                        class="p-1.5 rounded-full text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-black/[0.05] dark:hover:bg-white/[0.1] cursor-pointer"
                    >
                        <i class="pi pi-times text-xs"></i>
                    </button>
                </div>

                <!-- Media Preview Area -->
                <div class="p-4 sm:p-6 bg-[#f5f5f7] dark:bg-black/60 flex items-center justify-center min-h-[220px] max-h-[380px] overflow-hidden">
                    <template x-if="previewModal.file?.type === 'image'">
                        <img
                            :src="previewModal.file?.previewUrl || previewModal.file?.url"
                            :alt="previewModal.file?.name"
                            class="max-h-[340px] max-w-full rounded-2xl object-contain shadow-apple-card"
                        />
                    </template>

                    <template x-if="previewModal.file?.type === 'video'">
                        <div class="text-center w-full flex flex-col items-center bg-black/95 p-3 rounded-2xl">
                            <video
                                x-ref="videoPlayer"
                                :key="'video-' + previewModal.file?.id"
                                controls
                                playsinline
                                preload="metadata"
                                class="max-h-[300px] w-full max-w-full rounded-xl shadow-lg bg-black"
                            >
                                <source :src="previewModal.file?.previewUrl" :type="previewModal.file?.mimeType || 'video/mp4'">
                                Your browser does not support HTML5 video preview.
                            </video>
                            <div class="flex items-center justify-between w-full mt-2.5 px-1">
                                <h4 class="font-semibold text-xs sm:text-sm text-white truncate max-w-[200px] sm:max-w-xs" x-text="previewModal.file?.name"></h4>
                                <span class="text-[10px] text-gray-400" x-text="previewModal.file?.size"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="previewModal.file?.type === 'audio'">
                        <div class="text-center w-full max-w-sm p-2">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-[#30d158] flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                <i class="pi pi-volume-up text-2xl"></i>
                            </div>
                            <h4 class="font-bold text-xs sm:text-sm text-gray-900 dark:text-white truncate" x-text="previewModal.file?.name"></h4>
                            <p class="text-[11px] text-[#86868b] mt-0.5" x-text="previewModal.file?.size"></p>
                            <audio x-ref="audioPlayer" :key="'audio-' + previewModal.file?.id" controls preload="metadata" class="w-full mt-3">
                                <source :src="previewModal.file?.previewUrl" :type="previewModal.file?.mimeType || 'audio/mpeg'">
                                Your browser does not support HTML5 audio playback.
                            </audio>
                        </div>
                    </template>

                    <template x-if="previewModal.file?.type === 'document' || previewModal.file?.type === 'archive' || previewModal.file?.type === 'other'">
                        <div class="text-center p-4">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-[#ff9f0a] flex items-center justify-center mx-auto mb-2 shadow-2xs">
                                <i :class="getDocumentIcon(previewModal.file?.extension)" class="pi text-2xl"></i>
                            </div>
                            <h4 class="font-bold text-xs sm:text-sm text-gray-900 dark:text-white truncate max-w-xs" x-text="previewModal.file?.name"></h4>
                            <p class="text-[11px] text-[#86868b] mt-1"><span x-text="previewModal.file?.extension"></span> • <span x-text="previewModal.file?.size"></span></p>
                            <a :href="previewModal.file?.downloadUrl" class="inline-flex items-center gap-1.5 mt-3 px-4 py-1.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-xs font-semibold shadow-xs">
                                <i class="pi pi-download text-xs"></i>
                                <span>Download File</span>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between p-3 sm:p-4 border-t border-black/[0.06] dark:border-white/[0.08] bg-white dark:bg-[#1c1c1e]">
                    <button
                        @click="toggleStar(previewModal.file)"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-black/[0.04] dark:hover:bg-white/[0.06]"
                    >
                        <i :class="previewModal.file?.starred ? 'pi pi-star-fill text-amber-400' : 'pi pi-star text-gray-400'"></i>
                        <span x-text="previewModal.file?.starred ? 'Favorite' : 'Add to Favorites'"></span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button
                            @click="closePreview()"
                            type="button"
                            class="px-3.5 py-1.5 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-black/[0.04]"
                        >
                            Done
                        </button>
                        <button
                            @click="downloadFile(previewModal.file)"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white font-semibold text-xs shadow-xs cursor-pointer"
                        >
                            <i class="pi pi-download text-xs"></i>
                            Download
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Apple-style New Folder Sheet -->
        <div
            x-show="showCreateFolderModal"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-3 sm:p-4"
        >
            <!-- Backdrop -->
            <div
                x-show="showCreateFolderModal"
                @click="showCreateFolderModal = false"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-md"
            ></div>

            <!-- Dialog Content -->
            <div
                x-show="showCreateFolderModal"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-[#1c1c1e] rounded-2xl sm:rounded-3xl max-w-sm w-full border border-black/[0.08] dark:border-white/[0.12] shadow-2xl p-5"
            >
                <div class="flex items-center gap-2.5 mb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-[#0071e3] flex items-center justify-center">
                        <i class="pi pi-folder-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">New Folder</h3>
                        <p class="text-[11px] text-[#86868b]">Enter a name for this folder</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <input
                            type="text"
                            x-model="newFolderName"
                            @keydown.enter="submitCreateFolder()"
                            placeholder="Folder Name"
                            class="w-full px-3.5 py-2 rounded-xl border-none bg-black/[0.05] dark:bg-white/[0.08] text-xs sm:text-sm text-gray-900 dark:text-white placeholder-[#86868b] focus:ring-2 focus:ring-[#0071e3] focus:outline-none"
                            autofocus
                        />
                    </div>

                    <!-- Color Tags -->
                    <div>
                        <label class="block text-[11px] font-semibold text-[#86868b] mb-1.5">Color Tag</label>
                        <div class="flex items-center gap-2">
                            <template x-for="color in ['blue', 'purple', 'emerald', 'amber', 'rose']" :key="color">
                                <button
                                    type="button"
                                    @click="newFolderColor = color"
                                    :class="[
                                        newFolderColor === color ? 'ring-2 ring-offset-2 ring-[#0071e3] scale-110' : '',
                                        color === 'blue' ? 'bg-[#0071e3]' : '',
                                        color === 'purple' ? 'bg-[#bf5af2]' : '',
                                        color === 'emerald' ? 'bg-[#30d158]' : '',
                                        color === 'amber' ? 'bg-[#ff9f0a]' : '',
                                        color === 'rose' ? 'bg-[#ff375f]' : '',
                                    ]"
                                    class="w-5 h-5 rounded-full transition-all cursor-pointer"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-5 pt-3 border-t border-black/[0.05] dark:border-white/[0.07]">
                    <button
                        @click="showCreateFolderModal = false"
                        type="button"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-black/[0.04]"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitCreateFolder()"
                        type="button"
                        class="px-4 py-1.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white font-semibold text-xs shadow-xs cursor-pointer active:scale-95"
                    >
                        Create
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Apple-style Rename -->
        <div
            x-show="renameModal.open"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-3 sm:p-4"
        >
            <!-- Backdrop -->
            <div
                x-show="renameModal.open"
                @click="renameModal.open = false"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-md"
            ></div>

            <!-- Dialog Content -->
            <div
                x-show="renameModal.open"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-[#1c1c1e] rounded-2xl sm:rounded-3xl max-w-sm w-full border border-black/[0.08] dark:border-white/[0.12] shadow-2xl p-5"
            >
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white mb-0.5">
                    Rename <span x-text="renameModal.isFolder ? 'Folder' : 'File'"></span>
                </h3>
                <p class="text-[11px] text-[#86868b] mb-3">Enter the new title below</p>

                <input
                    type="text"
                    x-model="renameModal.newName"
                    @keydown.enter="submitRename()"
                    class="w-full px-3.5 py-2 rounded-xl border-none bg-black/[0.05] dark:bg-white/[0.08] text-xs sm:text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0071e3] focus:outline-none"
                />

                <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-black/[0.05] dark:border-white/[0.07]">
                    <button
                        @click="renameModal.open = false"
                        type="button"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-black/[0.04]"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitRename()"
                        type="button"
                        class="px-4 py-1.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white font-semibold text-xs shadow-xs cursor-pointer active:scale-95"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Apple-style Floating Upload Progress Island -->
        <div
            x-show="uploadModal.open"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-6 scale-95"
            class="fixed bottom-20 sm:bottom-6 right-3 sm:right-6 z-[95] max-w-sm w-[calc(100vw-24px)] sm:w-84 rounded-2xl bg-white/95 dark:bg-[#1c1c1e]/95 border border-black/[0.08] dark:border-white/[0.12] p-3.5 sm:p-4 shadow-apple-float backdrop-blur-2xl"
        >
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-[#0071e3] dark:text-[#0a84ff] flex items-center justify-center shrink-0">
                    <i class="pi pi-spin pi-spinner text-sm" x-show="uploadModal.progress < 100"></i>
                    <i class="pi pi-check text-sm text-emerald-500" x-show="uploadModal.progress >= 100"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate" x-text="uploadModal.fileName"></p>
                        <span class="text-[11px] font-bold text-[#0071e3] dark:text-[#0a84ff]" x-text="uploadModal.progress + '%'"></span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-[#86868b] mt-0.5">
                        <span x-text="uploadModal.status"></span>
                        <span x-text="uploadModal.bytesLoaded + ' / ' + uploadModal.bytesTotal"></span>
                    </div>

                    <!-- Real-time Progress Bar -->
                    <div class="w-full h-1.5 rounded-full bg-black/[0.06] dark:bg-white/[0.08] overflow-hidden mt-2.5">
                        <div
                            class="h-full bg-gradient-to-r from-[#0071e3] to-[#5856d6] transition-all duration-150 rounded-full"
                            :style="'width: ' + uploadModal.progress + '%'"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apple-style Floating Dynamic Island Pill Toast -->
        <div
            x-show="toast.show"
            x-cloak
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
            class="fixed top-16 sm:top-20 inset-x-0 mx-auto w-fit z-[110] flex items-center gap-2 px-4 py-2 rounded-full bg-black/85 dark:bg-white/90 text-white dark:text-black shadow-apple-float text-xs font-semibold backdrop-blur-xl border border-white/10 dark:border-black/10"
        >
            <i class="pi pi-check-circle text-[#30d158] text-xs"></i>
            <span x-text="toast.message"></span>
        </div>

    </div>

    <!-- Bucket Alpine Component Logic -->
    <script>
        function bucketManager() {
            return {
                viewMode: 'grid',
                activeFilter: 'all',
                searchQuery: '',
                currentFolder: null,
                isDragging: false,
                isUploading: false,
                openMenuFolderId: null,

                showCreateFolderModal: false,
                newFolderName: '',
                newFolderColor: 'blue',

                previewModal: {
                    open: false,
                    file: null,
                },

                renameModal: {
                    open: false,
                    item: null,
                    isFolder: false,
                    newName: '',
                },

                uploadModal: {
                    open: false,
                    progress: 0,
                    fileName: '',
                    status: '',
                    bytesLoaded: '0 B',
                    bytesTotal: '0 B',
                },

                toast: {
                    show: false,
                    message: '',
                    timeout: null,
                },

                folders: [],
                files: [],

                init() {
                    const initialFolders = @json($initialFolders ?? []);
                    const initialFiles = @json($initialFiles ?? []);

                    this.folders = (initialFolders || []).map(f => ({
                        id: f.id,
                        name: f.name,
                        color: f.color || 'blue',
                        updatedAt: f.formatted_updated_at || 'Recently',
                        filesCount: f.files_count || 0,
                    }));

                    this.files = (initialFiles || []).map(f => this.mapFile(f));
                },

                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                },

                mapFile(f) {
                    return {
                        id: f.id,
                        name: f.name,
                        type: f.category || 'other',
                        mimeType: f.mime_type || '',
                        extension: (f.extension || '').toUpperCase(),
                        size: f.formatted_size || this.formatBytes(f.size || 0),
                        originalSize: f.formatted_original_size || f.formatted_size,
                        savingsPercent: f.savings_percent || 0,
                        isCompressed: Boolean(f.is_compressed),
                        isOptimized: Boolean(f.is_optimized),
                        sizeBytes: parseInt(f.size || 0),
                        url: f.preview_url || ('/bucket/files/' + f.id + '/preview'),
                        previewUrl: f.preview_url || ('/bucket/files/' + f.id + '/preview'),
                        downloadUrl: f.download_url || ('/bucket/files/' + f.id + '/download'),
                        folderId: f.folder_id,
                        starred: Boolean(f.is_starred),
                        updatedAt: f.formatted_updated_at || 'Just now',
                    };
                },

                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.show = true;
                    if (this.toast.timeout) clearTimeout(this.toast.timeout);
                    this.toast.timeout = setTimeout(() => {
                        this.toast.show = false;
                    }, 2500);
                },

                // Filters and Search
                filteredFolders() {
                    let list = this.folders;
                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase();
                        list = list.filter(f => f.name.toLowerCase().includes(q));
                    }
                    return list;
                },

                filteredFiles() {
                    let list = this.files;

                    // Folder context
                    if (this.currentFolder) {
                        list = list.filter(f => f.folderId == this.currentFolder.id);
                    } else if (this.activeFilter === 'folders') {
                        return [];
                    }

                    // Category filter
                    if (this.activeFilter !== 'all' && this.activeFilter !== 'folders') {
                        if (this.activeFilter === 'starred') {
                            list = list.filter(f => f.starred);
                        } else {
                            list = list.filter(f => f.type === this.activeFilter);
                        }
                    }

                    // Search Query
                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase();
                        list = list.filter(f => f.name.toLowerCase().includes(q));
                    }

                    return list;
                },

                getFolderFileCount(folderId) {
                    return this.files.filter(f => f.folderId == folderId).length;
                },

                countFilesByType(type) {
                    return this.files.filter(f => f.type === type).length;
                },

                countStarredFiles() {
                    return this.files.filter(f => f.starred).length;
                },

                // Folder Navigation
                openFolder(folder) {
                    this.currentFolder = folder;
                },

                navigateToRoot() {
                    this.currentFolder = null;
                },

                toggleFolderMenu(folderId) {
                    this.openMenuFolderId = this.openMenuFolderId === folderId ? null : folderId;
                },

                // Create Folder
                openNewFolderModal() {
                    this.newFolderName = '';
                    this.newFolderColor = 'blue';
                    this.showCreateFolderModal = true;
                },

                async submitCreateFolder() {
                    const name = this.newFolderName.trim();
                    if (!name) return;

                    try {
                        const res = await fetch('{{ route('bucket.folder.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken()
                            },
                            body: JSON.stringify({
                                name: name,
                                color: this.newFolderColor,
                                parent_id: this.currentFolder ? this.currentFolder.id : null
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            const newFolder = {
                                id: data.folder.id,
                                name: data.folder.name,
                                color: data.folder.color,
                                updatedAt: data.folder.formatted_updated_at || 'Just now',
                                filesCount: 0
                            };
                            this.folders.unshift(newFolder);
                            this.showCreateFolderModal = false;
                            this.showToast(`Folder "${name}" created`);
                        } else {
                            this.showToast(data.message || 'Failed to create folder');
                        }
                    } catch (e) {
                        this.showToast('Network error creating folder');
                    }
                },

                async deleteFolder(folderId) {
                    const folder = this.folders.find(f => f.id === folderId);
                    const name = folder ? folder.name : 'Folder';

                    try {
                        const res = await fetch(`/bucket/folders/${folderId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken()
                            }
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.folders = this.folders.filter(f => f.id !== folderId);
                            this.files = this.files.filter(f => f.folderId != folderId);
                            if (this.currentFolder && this.currentFolder.id === folderId) {
                                this.currentFolder = null;
                            }
                            this.showToast(`Deleted folder "${name}"`);
                        } else {
                            this.showToast(data.message || 'Error deleting folder');
                        }
                    } catch (e) {
                        this.showToast('Network error deleting folder');
                    }
                },

                // Uploading
                handleDrop(event) {
                    this.isDragging = false;
                    if (event.dataTransfer.files && event.dataTransfer.files.length > 0) {
                        this.processUploadedFiles(event.dataTransfer.files);
                    }
                },

                handleFileUpload(event) {
                    if (event.target.files && event.target.files.length > 0) {
                        this.processUploadedFiles(event.target.files);
                        event.target.value = '';
                    }
                },

                processUploadedFiles(fileList) {
                    if (!fileList || fileList.length === 0) return;

                    if (!this.currentFolder) {
                        this.showToast('Please open or create a folder first before uploading files.');
                        return;
                    }

                    // Check file size (512MB limit)
                    const maxBytes = 512 * 1024 * 1024;
                    for (let i = 0; i < fileList.length; i++) {
                        if (fileList[i].size > maxBytes) {
                            this.showToast(`"${fileList[i].name}" exceeds 512MB limit.`);
                            return;
                        }
                    }

                    const formData = new FormData();
                    Array.from(fileList).forEach(file => {
                        formData.append('files[]', file);
                    });

                    if (this.currentFolder) {
                        formData.append('folder_id', this.currentFolder.id);
                    }

                    const fileCount = fileList.length;
                    const firstName = fileList[0].name;
                    const totalBytes = Array.from(fileList).reduce((acc, f) => acc + (f.size || 0), 0);

                    this.isUploading = true;
                    this.uploadModal = {
                        open: true,
                        progress: 0,
                        fileName: fileCount === 1 ? firstName : `${firstName} (+${fileCount - 1} more)`,
                        status: 'Preparing upload...',
                        bytesLoaded: '0 B',
                        bytesTotal: this.formatBytes(totalBytes)
                    };

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route('bucket.upload') }}', true);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken());

                    xhr.upload.onprogress = (e) => {
                        if (e.lengthComputable) {
                            const pct = Math.round((e.loaded / e.total) * 100);
                            this.uploadModal.progress = Math.min(99, pct);
                            this.uploadModal.bytesLoaded = this.formatBytes(e.loaded);
                            if (pct >= 99) {
                                this.uploadModal.status = 'Processing & saving to bucket...';
                            } else {
                                this.uploadModal.status = `Uploading (${pct}%)...`;
                            }
                        }
                    };

                    xhr.onload = () => {
                        this.isUploading = false;
                        this.uploadModal.progress = 100;

                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (xhr.status >= 200 && xhr.status < 300 && data.success && data.files) {
                                data.files.forEach(uploaded => {
                                    this.files.unshift(this.mapFile(uploaded));
                                });
                                this.uploadModal.status = 'Upload complete!';
                                this.showToast(data.message || 'Files uploaded successfully');
                                setTimeout(() => {
                                    this.uploadModal.open = false;
                                }, 1200);
                            } else {
                                this.uploadModal.status = 'Upload failed';
                                this.showToast(data.message || 'Upload failed. File may be too large or format unsupported.');
                                setTimeout(() => {
                                    this.uploadModal.open = false;
                                }, 3500);
                            }
                        } catch (err) {
                            this.uploadModal.status = 'Server error';
                            this.showToast('Server error while saving file. Check file size.');
                            setTimeout(() => {
                                this.uploadModal.open = false;
                            }, 3500);
                        }
                    };

                    xhr.onerror = () => {
                        this.isUploading = false;
                        this.uploadModal.status = 'Network error';
                        this.showToast('Network error during upload. Please check connection.');
                        setTimeout(() => {
                            this.uploadModal.open = false;
                        }, 3500);
                    };

                    xhr.send(formData);
                },

                // Downloading
                downloadFile(file) {
                    if (!file) return;
                    const url = file.downloadUrl || `/bucket/files/${file.id}/download`;
                    window.location.href = url;
                    this.showToast(`Downloading "${file.name}"`);
                },

                async deleteFile(fileId) {
                    const file = this.files.find(f => f.id === fileId);
                    const name = file ? file.name : 'File';

                    try {
                        const res = await fetch(`/bucket/files/${fileId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken()
                            }
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.files = this.files.filter(f => f.id !== fileId);
                            this.showToast(`Removed "${name}"`);
                            if (this.previewModal.open && this.previewModal.file?.id === fileId) {
                                this.closePreview();
                            }
                        } else {
                            this.showToast(data.message || 'Error deleting file');
                        }
                    } catch (e) {
                        this.showToast('Network error deleting file');
                    }
                },

                async toggleStar(file) {
                    if (!file) return;
                    const original = file.starred;
                    file.starred = !original;

                    try {
                        const res = await fetch(`/bucket/files/${file.id}/star`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken()
                            }
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            file.starred = data.starred;
                            this.showToast(file.starred ? `Favorited "${file.name}"` : `Removed from favorites`);
                        } else {
                            file.starred = original;
                        }
                    } catch (e) {
                        file.starred = original;
                    }
                },

                openPreview(file) {
                    this.previewModal.file = file;
                    this.previewModal.open = true;
                    this.$nextTick(() => {
                        if (this.$refs.videoPlayer) {
                            try {
                                this.$refs.videoPlayer.load();
                            } catch (e) {}
                        }
                        if (this.$refs.audioPlayer) {
                            try {
                                this.$refs.audioPlayer.load();
                            } catch (e) {}
                        }
                    });
                },

                closePreview() {
                    if (this.$refs.videoPlayer) {
                        try {
                            this.$refs.videoPlayer.pause();
                        } catch (e) {}
                    }
                    if (this.$refs.audioPlayer) {
                        try {
                            this.$refs.audioPlayer.pause();
                        } catch (e) {}
                    }
                    this.previewModal.open = false;
                    this.previewModal.file = null;
                },

                // Renaming
                openRenameModal(item, isFolder) {
                    this.renameModal.item = item;
                    this.renameModal.isFolder = isFolder;
                    this.renameModal.newName = item.name;
                    this.renameModal.open = true;
                },

                async submitRename() {
                    const newName = this.renameModal.newName.trim();
                    if (!newName || !this.renameModal.item) return;

                    const item = this.renameModal.item;
                    const isFolder = this.renameModal.isFolder;

                    const url = isFolder ? `/bucket/folders/${item.id}` : `/bucket/files/${item.id}/rename`;

                    try {
                        const res = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken()
                            },
                            body: JSON.stringify({ name: newName })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            item.name = newName;
                            this.renameModal.open = false;
                            this.showToast(`Renamed to "${newName}"`);
                        } else {
                            this.showToast(data.message || 'Rename failed');
                        }
                    } catch (e) {
                        this.showToast('Network error renaming item');
                    }
                },

                // Storage Computations
                calculateTotalSizeBytes() {
                    return this.files.reduce((acc, f) => acc + (f.sizeBytes || 0), 0);
                },

                calculateTotalSizeFormatted() {
                    return this.formatBytes(this.calculateTotalSizeBytes());
                },

                getCategoryTotalBytes(type) {
                    return this.files.filter(f => f.type === type).reduce((acc, f) => acc + (f.sizeBytes || 0), 0);
                },

                getCategoryTotalFormatted(type) {
                    return this.formatBytes(this.getCategoryTotalBytes(type));
                },

                getStorageCategoryPercent(type) {
                    const total = this.calculateTotalSizeBytes();
                    if (total === 0) return 0;
                    const used = this.getCategoryTotalBytes(type);
                    return Math.max(1, ((used / total) * 100).toFixed(1));
                },

                formatBytes(bytes) {
                    if (!bytes || bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                },

                // Styling helpers (Apple pastel tones)
                getFolderBgClass(color) {
                    const map = {
                        blue: 'bg-blue-50 dark:bg-blue-950/40',
                        purple: 'bg-purple-50 dark:bg-purple-950/40',
                        emerald: 'bg-emerald-50 dark:bg-emerald-950/40',
                        amber: 'bg-amber-50 dark:bg-amber-950/40',
                        rose: 'bg-rose-50 dark:bg-rose-950/40'
                    };
                    return map[color] || map.blue;
                },

                getFolderIconClass(color) {
                    const map = {
                        blue: 'text-[#0071e3] dark:text-[#0a84ff]',
                        purple: 'text-[#bf5af2]',
                        emerald: 'text-[#30d158]',
                        amber: 'text-[#ff9f0a]',
                        rose: 'text-[#ff375f]'
                    };
                    return map[color] || map.blue;
                },

                getFileIconBadgeBg(type) {
                    const map = {
                        image: 'bg-sky-50 dark:bg-sky-950/50 text-[#0071e3]',
                        video: 'bg-purple-50 dark:bg-purple-950/50 text-[#bf5af2]',
                        audio: 'bg-emerald-50 dark:bg-emerald-950/50 text-[#30d158]',
                        document: 'bg-amber-50 dark:bg-amber-950/50 text-[#ff9f0a]',
                        archive: 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-500'
                    };
                    return map[type] || 'bg-gray-100 dark:bg-[#2c2c2e] text-gray-500';
                },

                getFileIconClass(type, extension) {
                    if (type === 'image') return 'pi-image';
                    if (type === 'video') return 'pi-video';
                    if (type === 'audio') return 'pi-volume-up';
                    return this.getDocumentIcon(extension);
                },

                getDocumentIcon(ext) {
                    const e = (ext || '').toUpperCase();
                    if (e === 'PDF') return 'pi-file-pdf';
                    if (e === 'ZIP' || e === 'RAR' || e === 'TAR' || e === '7Z') return 'pi-file-import';
                    if (e === 'DOC' || e === 'DOCX') return 'pi-file-word';
                    if (e === 'XLS' || e === 'XLSX' || e === 'CSV') return 'pi-file-excel';
                    return 'pi-file';
                }
            };
        }
    </script>
</x-app-layout>
