@props(['categories' => []])

@php
    $normalizedCategories = collect($categories ?? [])->map(function ($category) {
        return [
            'id' => $category['id'] ?? null,
            'name' => $category['name'] ?? 'Unknown',
            'slug' => $category['slug'] ?? null,
            'description' => $category['description'] ?? null,
            'url' => $category['url'] ?? '#',
            'products' => collect($category['products'] ?? [])->map(function ($product) {
                return [
                    'id' => $product['id'] ?? null,
                    'name' => $product['name'] ?? 'Product',
                    'price_formatted' => $product['price_formatted'] ?? format_price($product['price'] ?? 0),
                    'url' => $product['url'] ?? '#',
                    'image' => $product['image'] ?? 'https://source.unsplash.com/400x400/?product',
                ];
            })->filter(fn ($product) => filled($product['id']))->values(),
        ];
    })->filter(fn ($category) => filled($category['id']))->values();
@endphp

<div x-data="megaMenu({ categories: @js($normalizedCategories) })"
     class="relative"
     x-id="['mega-menu']"
     @mouseenter="setHover(true)"
     @mouseleave="setHover(false)"
     @keydown.escape.window="close()">
  <button type="button"
          class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-neutral-900/80 px-5 py-2 text-sm font-semibold text-neutral-100 shadow-sm transition hover:bg-neutral-900/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400"
          x-ref="trigger"
          :aria-expanded="open.toString()"
          :aria-controls="$id('mega-menu')"
          @click.stop="toggle()">
    <i class="fa-solid fa-layer-group text-xs"></i>
    <span>{{ __('common.categories') }}</span>
    <i class="fa-solid fa-chevron-down text-[10px] opacity-70 transition duration-200" :class="open ? 'rotate-180' : ''"></i>
  </button>

  <section x-cloak
           x-show="open"
           x-transition.origin.top
           :id="$id('mega-menu')"
           role="region"
           aria-label="{{ __('Browse Categories') }}"
           tabindex="-1"
           class="mega-menu-panel z-[150]"
           :style="panelStyle"
           @mouseenter="setHover(true)"
           @mouseleave="setHover(false)"
           @click.outside="close()">
    <div class="mega-menu-header">
      <div>
        <h2 class="text-lg font-semibold tracking-tight">{{ __('Browse Categories') }}</h2>
        <p class="text-sm text-white/60">{{ __('Select a category to see featured products.') }}</p>
      </div>
      <button type="button" class="mega-menu-close" @click="close()">
        <i class="fa-solid fa-xmark"></i>
        <span class="sr-only">{{ __('Close mega menu') }}</span>
      </button>
    </div>

    <div class="mega-menu-grid">
      <nav class="mega-menu-sidebar" aria-label="{{ __('Category list') }}">
        <template x-for="category in categories" :key="category.id">
          <button type="button"
                  class="mega-menu-sidebar-item"
                  :class="activeCategory && activeCategory.id === category.id ? 'is-active' : ''"
                  @mouseenter="setActive(category.id)"
                  @focus="setActive(category.id)"
                  @click="setActive(category.id)">
            <span class="text-base font-semibold" x-text="category.name"></span>
            <p class="text-xs leading-relaxed text-white/60" x-show="category.description" x-text="category.description"></p>
          </button>
        </template>

        <template x-if="!categories.length">
          <div class="mega-menu-empty">{{ __('No categories available yet.') }}</div>
        </template>
      </nav>

      <div class="mega-menu-content" x-show="activeCategory" x-transition.opacity>
        <div class="mega-menu-content-header">
          <div>
            <h3 class="text-xl font-semibold tracking-tight" x-text="activeCategory?.name"></h3>
            <p class="mt-1 text-sm text-white/60" x-text="activeCategory?.description"></p>
          </div>
          <a :href="activeCategory?.url"
             class="mega-menu-view-all">
            {{ __('View Category') }}
            <i class="fa-solid fa-arrow-right-long text-[10px]"></i>
          </a>
        </div>

        <div class="mega-menu-product-grid">
          <template x-for="product in activeCategory?.products" :key="product.id">
            <a :href="product.url" class="mega-menu-product-card group">
              <div class="mega-menu-product-name" x-text="product.name"></div>
              <div class="mega-menu-product-meta">
                <span x-text="product.price_formatted"></span>
                <i class="fa-solid fa-arrow-right-long transition-transform duration-200 group-hover:translate-x-1"></i>
              </div>
            </a>
          </template>

          <template x-if="activeCategory && !activeCategory.products.length">
            <div class="mega-menu-empty">{{ __('Products coming soon.') }}</div>
          </template>
        </div>
      </div>

      <aside class="mega-menu-preview">
        <template x-if="activeCategory && activeCategory.products.length">
          <div class="grid grid-cols-2 gap-3">
            <template x-for="product in activeCategory.products.slice(0,4)" :key="product.id + '-preview'">
              <a :href="product.url" class="group overflow-hidden rounded-2xl bg-white/10">
                <img :src="product.image" :alt="product.name" class="h-24 w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy"/>
              </a>
            </template>
          </div>
        </template>
        <a :href="activeCategory?.url ?? '#'" class="mega-menu-preview-link">
          {{ __('Explore All Products') }}
          <i class="fa-solid fa-arrow-right-long ml-2 text-[10px]"></i>
        </a>
      </aside>
    </div>
  </section>
</div>
