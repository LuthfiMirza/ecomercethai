<div x-data="{open:false}" class="relative">
  <button @click="open = !open" class="btn" type="button">Theme</button>
  <div x-cloak x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-64 card p-4 z-50">
    <div class="space-y-3 text-sm">
      <div>
        <div class="mb-1 font-medium">Mode</div>
        <div class="flex items-center gap-2">
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.mode==='light'}" @click="$store.theme.setMode('light')">Light</button>
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.mode==='dark'}" @click="$store.theme.setMode('dark')">Dark</button>
        </div>
      </div>
      <div>
        <div class="mb-1 font-medium">Primary</div>
        <div class="flex items-center gap-2">
          <button class="h-8 w-8 rounded-full border" style="background:#3C50E0" @click="$store.theme.setColor('blue')" :class="{'ring-2 ring-primary-600': $store.theme.color==='blue'}"></button>
          <button class="h-8 w-8 rounded-full border" style="background:#059669" @click="$store.theme.setColor('emerald')" :class="{'ring-2 ring-primary-600': $store.theme.color==='emerald'}"></button>
          <button class="h-8 w-8 rounded-full border" style="background:#7C3AED" @click="$store.theme.setColor('violet')" :class="{'ring-2 ring-primary-600': $store.theme.color==='violet'}"></button>
          <button class="h-8 w-8 rounded-full border" style="background:#E11D48" @click="$store.theme.setColor('rose')" :class="{'ring-2 ring-primary-600': $store.theme.color==='rose'}"></button>
        </div>
      </div>
      <div>
        <div class="mb-1 font-medium">Radius</div>
        <div class="flex items-center gap-2">
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.radius==='md'}" @click="$store.theme.setRadius('md')">md</button>
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.radius==='lg'}" @click="$store.theme.setRadius('lg')">lg</button>
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.radius==='2xl'}" @click="$store.theme.setRadius('2xl')">2xl</button>
        </div>
      </div>
      <div>
        <div class="mb-1 font-medium">Density</div>
        <div class="flex items-center gap-2">
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.density==='comfortable'}" @click="$store.theme.setDensity('comfortable')">Comfortable</button>
          <button class="btn" :class="{'ring-2 ring-primary-600': $store.theme.density==='compact'}" @click="$store.theme.setDensity('compact')">Compact</button>
        </div>
      </div>
    </div>
  </div>
</div>

