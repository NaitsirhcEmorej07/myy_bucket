@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-black/[0.08] dark:border-white/[0.12] bg-white dark:bg-[#2c2c2e] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-[#0071e3] dark:focus:border-[#0a84ff] focus:ring-2 focus:ring-[#0071e3]/20 dark:focus:ring-[#0a84ff]/30 rounded-xl sm:rounded-2xl text-xs sm:text-sm transition-all shadow-2xs py-2 sm:py-2.5 px-3 sm:px-3.5 block w-full']) }}>
