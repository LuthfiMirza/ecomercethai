<form method="POST" action="{{ route('register') }}" class="space-y-6">
    @csrf
    <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-neutral-700" data-i18n="register.name_label">Full name</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-rose-300">
                <i class="fa-regular fa-user"></i>
            </span>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                   placeholder="Enter your full name" data-i18n-placeholder="register.name_placeholder"
                   class="w-full rounded-xl border border-[#ffd0db] bg-white/90 px-10 py-3 text-sm text-neutral-800 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100"/>
        </div>
        @error('name')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-neutral-700" data-i18n="register.email_label">Email address</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-rose-300">
                <i class="fa-regular fa-envelope"></i>
            </span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   placeholder="Enter your email" data-i18n-placeholder="register.email_placeholder"
                   class="w-full rounded-xl border border-[#ffd0db] bg-white/90 px-10 py-3 text-sm text-neutral-800 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100"/>
        </div>
        @error('email')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-neutral-700" data-i18n="register.password_label">Password</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-rose-300">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="Create a strong password" data-i18n-placeholder="register.password_placeholder"
                   class="w-full rounded-xl border border-[#ffd0db] bg-white/90 px-10 py-3 text-sm text-neutral-800 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100"/>
        </div>
        @error('password')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="password_confirmation" class="block text-sm font-medium text-neutral-700" data-i18n="register.password_confirmation_label">Confirm password</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-rose-300">
                <i class="fa-solid fa-shield-heart"></i>
            </span>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="Repeat your password" data-i18n-placeholder="register.password_confirmation_placeholder"
                   class="w-full rounded-xl border border-[#ffd0db] bg-white/90 px-10 py-3 text-sm text-neutral-800 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100"/>
        </div>
    </div>
    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-[#ff869a] to-[#ff6f73] py-3 font-semibold text-white shadow-[0_18px_35px_-15px_rgba(255,97,125,0.8)] transition hover:opacity-90" data-i18n="register.submit">Sign up</button>
</form>
