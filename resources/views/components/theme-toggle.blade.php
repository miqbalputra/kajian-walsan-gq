<div x-data="{ 
    isDark: document.documentElement.classList.contains('dark'),
    init() {
        this.isDark = document.documentElement.classList.contains('dark');
    },
    toggle() {
        this.isDark = !this.isDark;
        if (this.isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}" class="flex items-center shrink-0">
    <button @click="toggle()" type="button"
        class="relative inline-flex h-8 w-14 items-center rounded-full transition-all duration-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 group {{ $class ?? '' }}"
        :class="isDark ? 'bg-slate-800 border border-slate-700' : 'bg-gray-100 border border-gray-200'"
        aria-label="Toggle Theme">

        <!-- Icons inside track (Static) -->
        <div class="absolute inset-0 flex items-center justify-between px-1.5 pointer-events-none">
            <span class="material-symbols-rounded text-[14px] leading-none transition-colors duration-300"
                :class="isDark ? 'text-slate-600' : 'text-amber-500'">light_mode</span>
            <span class="material-symbols-rounded text-[14px] leading-none transition-colors duration-300"
                :class="isDark ? 'text-indigo-400' : 'text-gray-300'">dark_mode</span>
        </div>

        <!-- Sliding Circle -->
        <span
            class="inline-block h-6 w-6 transform rounded-full bg-white keep-white shadow-md transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] relative z-10 flex items-center justify-center"
            :class="isDark ? 'translate-x-7' : 'translate-x-1'">
            <span class="material-symbols-rounded text-base font-bold transition-all duration-500"
                :class="isDark ? 'text-slate-900 rotate-[360deg]' : 'text-amber-500 rotate-0'"
                x-text="isDark ? 'dark_mode' : 'light_mode'">
            </span>
        </span>
    </button>
</div>