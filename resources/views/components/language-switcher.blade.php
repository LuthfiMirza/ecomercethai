<div x-data="langDd()" class="relative">
  <button type="button"
          @click="open = !open"
          @keydown.escape.window="open=false"
          @click.outside="open=false"
          :aria-expanded="open.toString()"
          aria-haspopup="menu"
          aria-label="Language switcher"
          class="inline-flex items-center gap-1 rounded-md border border-neutral-300 dark:border-neutral-700 bg-white/80 dark:bg-neutral-800/80 px-2 py-1 text-xs shadow-sm hover:bg-white dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-accent-500">
    <span class="select-none" x-text="flag()"></span>
    <span class="font-medium" x-text="lang.toUpperCase()"></span>
    <i class="fa-solid fa-chevron-down text-[10px] opacity-70"></i>
  </button>

  <div x-cloak x-show="open" x-transition.opacity x-transition.scale.origin.top
       role="menu" aria-label="Language menu"
       class="absolute right-0 mt-2 w-36 rounded-md border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-elevated overflow-hidden z-50">
    <button type="button" role="menuitem"
            @click="set('en')"
            class="w-full text-left px-3 py-2 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-accent-500">
      <span class="mr-2">🇬🇧</span> English
    </button>
    <button type="button" role="menuitem"
            @click="set('th')"
            class="w-full text-left px-3 py-2 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-accent-500">
      <span class="mr-2">🇹🇭</span> ไทย (Thai)
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
