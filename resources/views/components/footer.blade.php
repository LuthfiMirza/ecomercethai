<footer class="mt-16 bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800" role="contentinfo">
  <div class="container py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">About</h2>
        <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-300">Your trusted partner for all computer and hardware needs in Thailand.</p>
      </div>
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">Links</h2>
        <ul class="mt-3 space-y-2 text-sm">
          <li><a href="{{ url('/catalog') }}" class="hover:text-accent-600">Catalog</a></li>
          <li><a href="{{ url('/deals') }}" class="hover:text-accent-600">Deals</a></li>
          <li><a href="{{ url('/support') }}" class="hover:text-accent-600">Support</a></li>
        </ul>
      </div>
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">Support</h2>
        <ul class="mt-3 space-y-2 text-sm">
          <li><a href="#" class="hover:text-accent-600">Shipping</a></li>
          <li><a href="#" class="hover:text-accent-600">Warranty</a></li>
          <li><a href="#" class="hover:text-accent-600">Returns</a></li>
        </ul>
      </div>
      <div>
        <h2 class="text-sm font-semibold tracking-wide text-neutral-500">Newsletter</h2>
        <form class="mt-3 flex gap-2" action="#" method="post">
          <label class="sr-only" for="newsletter-email">Email</label>
          <input id="newsletter-email" type="email" placeholder="Email address" class="flex-1 rounded-md border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"/>
          <button class="px-4 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">Subscribe</button>
        </form>
      </div>
    </div>
    <div class="mt-8 flex items-center justify-between text-xs text-neutral-500">
      <p>&copy; {{ date('Y') }} Toko Thailand</p>
      <div class="flex items-center gap-3">
        <a href="#" aria-label="Facebook" class="hover:text-accent-600"><i class="fa-brands fa-facebook"></i></a>
        <a href="#" aria-label="Instagram" class="hover:text-accent-600"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="YouTube" class="hover:text-accent-600"><i class="fa-brands fa-youtube"></i></a>
      </div>
    </div>
  </div>
</footer>
