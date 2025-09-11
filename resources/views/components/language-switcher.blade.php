<div x-data="langSwitch()" class="inline-flex items-center gap-2">
  <label for="lang" class="sr-only">Language</label>
  <select id="lang" x-model="lang" @change="save" class="rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-xs py-1 px-2 focus:outline-none focus:ring-2 focus:ring-accent-500">
    <option value="en">EN</option>
    <option value="th">TH</option>
  </select>
  <script>
    function langSwitch(){
      return {
        lang: localStorage.getItem('lang') || 'en',
        save(){ localStorage.setItem('lang', this.lang); document.dispatchEvent(new CustomEvent('i18n:change', { detail: { lang: this.lang } })); }
      }
    }
  </script>
</div>
