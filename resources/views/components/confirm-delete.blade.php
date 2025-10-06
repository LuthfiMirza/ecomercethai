@props(['action'])
<div x-data="{ open: false }" class="inline-block">
    <button @click="open = true" type="button" class="btn-outline text-xs text-rose-600 border-rose-300 hover:bg-rose-50">{{ $slot ?? 'Delete' }}</button>
    <div x-show="open" x-cloak x-transition class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur" @click="open=false"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="soft-card max-w-md w-full space-y-4">
                <div class="flex items-center gap-3">
                    <span class="icon-circle bg-rose-500/20 text-rose-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-700 dark:text-white">Confirm Deletion</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-300">Are you sure you want to delete this item? This action cannot be undone.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="open=false" class="btn-ghost text-sm">Cancel</button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-primary text-sm bg-gradient-to-r from-rose-500 to-rose-600">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
