@props(['class' => ''])

<button type="button"
    x-data
    x-on:click="darkMode = !darkMode"
    class="icon-circle {{ $class }}"
    aria-label="Toggle dark mode">
    <svg x-show="!darkMode" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
    </svg>
    <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
    </svg>
    <span class="sr-only">Toggle dark mode</span>
</button>
