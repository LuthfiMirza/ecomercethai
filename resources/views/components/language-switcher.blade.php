<div x-data="langDd()" class="relative">
  <button type="button"
          @click="open = !open"
          @keydown.escape.window="open=false"
          @click.outside="open=false"
          :aria-expanded="open.toString()"
          aria-haspopup="menu"
          aria-label="Language switcher"
          class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white text-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 px-3 py-1.5 text-xs font-semibold shadow-sm transition-colors hover:bg-rose-50/80 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-neutral-900">
    <span class="select-none text-base leading-none" x-text="flag()"></span>
    <span class="font-semibold tracking-wide" x-text="lang.toUpperCase()"></span>
    <i class="fa-solid fa-chevron-down text-[10px] opacity-60"></i>
  </button>

  <div x-cloak x-show="open" x-transition.opacity x-transition.scale.origin.top
       role="menu" aria-label="Language menu"
       class="absolute right-0 mt-2 w-40 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white/95 dark:bg-neutral-900/95 shadow-elevated overflow-hidden z-50">
    <button type="button" role="menuitem"
            @click="set('en')"
            :class="['flex w-full items-center gap-3 px-4 py-2.5 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-accent-500', lang === 'en' ? 'bg-rose-50/70 text-rose-500 dark:bg-neutral-800/70 dark:text-rose-300' : 'text-neutral-600 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800']">
      <span class="text-base">🇬🇧</span>
      <span>English</span>
    </button>
    <button type="button" role="menuitem"
            @click="set('th')"
            :class="['flex w-full items-center gap-3 px-4 py-2.5 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-accent-500', lang === 'th' ? 'bg-rose-50/70 text-rose-500 dark:bg-neutral-800/70 dark:text-rose-300' : 'text-neutral-600 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800']">
      <span class="text-base">🇹🇭</span>
      <span>ไทย (Thai)</span>
    </button>
  </div>

  <script>
    function langDd(){
      return {
        open:false,
        lang: localStorage.getItem('lang') || 'en',
        flag(){ return this.lang === 'th' ? '🇹🇭' : '🇬🇧' },
        set(code){
          this.lang = code; localStorage.setItem('lang', code);
          this.open = false; document.dispatchEvent(new CustomEvent('i18n:change', { detail: { lang: code } }));
        },
      }
    }
  </script>
</div>
