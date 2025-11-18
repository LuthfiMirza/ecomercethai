<footer class="mt-16 bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800" role="contentinfo">
  <div class="container py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div>
        <div class="flex items-center gap-3 mb-3">
          <img src="{{ asset('image/logo.jpg') }}" alt="{{ config('app.name', 'Lungpaeit') }}" class="h-10 w-10 rounded-full object-cover shadow-sm" loading="lazy">
          <span class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ config('app.name', 'Lungpaeit') }}</span>
        </div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">{{ __('footer.about_title') }}</h2>
        <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-300">{{ __('footer.about_text') }}</p>
      </div>
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">{{ __('footer.links_title') }}</h2>
        <ul class="mt-3 space-y-2 text-sm">
          <li><a href="{{ route('catalog') }}" class="hover:text-accent-600">{{ __('common.product') }}</a></li>
          <li><a href="{{ route('contact') }}" class="hover:text-accent-600">{{ __('common.contact_us') }}</a></li>
          <li><a href="{{ route('faqs') }}" class="hover:text-accent-600">{{ __('common.faqs') }}</a></li>
        </ul>
      </div>
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">{{ __('footer.support_title') }}</h2>
        <ul class="mt-3 space-y-2 text-sm">
          <li><a href="#" class="hover:text-accent-600">{{ __('footer.support_shipping') }}</a></li>
          <li><a href="#" class="hover:text-accent-600">{{ __('footer.support_warranty') }}</a></li>
          <li><a href="#" class="hover:text-accent-600">{{ __('footer.support_returns') }}</a></li>
        </ul>
      </div>
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">{{ __('footer.newsletter_title') }}</h2>
        <form class="mt-3 flex gap-2" action="#" method="post">
          <label class="sr-only" for="newsletter-email">{{ __('footer.newsletter_label') }}</label>
          <input id="newsletter-email" type="email" placeholder="{{ __('footer.newsletter_placeholder') }}" class="flex-1 rounded-md border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"/>
          <button class="px-4 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">{{ __('footer.newsletter_button') }}</button>
        </form>
      </div>
    </div>
    <div class="mt-8 flex items-center justify-between text-xs text-neutral-500">
      <p>&copy; {{ date('Y') }} {{ config('app.name', 'Lungpaeit') }}</p>
      <div class="flex items-center gap-4 text-sm md:text-base">
        <a href="#" aria-label="{{ __('footer.social_facebook') }}" class="flex items-center gap-2 hover:text-accent-600">
          <i class="fa-brands fa-facebook text-lg md:text-xl"></i>
          <span class="hidden sm:inline">{{ __('footer.social_facebook') }}</span>
        </a>
        <a href="#" aria-label="{{ __('footer.social_instagram') }}" class="flex items-center gap-2 hover:text-accent-600">
          <i class="fa-brands fa-instagram text-lg md:text-xl"></i>
          <span class="hidden sm:inline">{{ __('footer.social_instagram') }}</span>
        </a>
        <a href="#" aria-label="{{ __('footer.social_youtube') }}" class="flex items-center gap-2 hover:text-accent-600">
          <i class="fa-brands fa-youtube text-lg md:text-xl"></i>
          <span class="hidden sm:inline">{{ __('footer.social_youtube') }}</span>
        </a>
      </div>
    </div>
  </div>
</footer>
