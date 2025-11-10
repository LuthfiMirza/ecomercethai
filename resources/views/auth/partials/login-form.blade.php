<form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf
    <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-neutral-700" data-i18n="login.email_label">Email address</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-orange-300">
                <i class="fa-regular fa-envelope"></i>
            </span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   placeholder="Enter your email" data-i18n-placeholder="login.email_placeholder"
                   class="w-full rounded-xl border border-orange-200 bg-white px-10 py-3 text-sm text-neutral-800 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-100"/>
        </div>
        @error('email')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-neutral-700" data-i18n="login.password_label">Password</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-orange-300">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="Enter your password" data-i18n-placeholder="login.password_placeholder"
                   class="w-full rounded-xl border border-orange-200 bg-white px-10 py-3 text-sm text-neutral-800 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-100"/>
        </div>
        @error('password')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex items-center justify-between">
        <label class="flex items-center text-sm text-neutral-600">
            <input type="checkbox" name="remember" class="mr-2 h-4 w-4 rounded border-orange-300 text-orange-500 focus:ring-orange-200">
            <span data-i18n="login.remember_me">Remember me</span>
        </label>
        <a href="#" class="text-sm font-medium text-orange-500 hover:text-orange-600" data-i18n="login.forgot_password">Forgot password?</a>
    </div>
    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-[#ff9f68] to-[#ff7043] py-3 font-semibold text-white shadow-[0_20px_40px_-18px_rgba(255,112,67,0.8)] transition hover:opacity-90" data-i18n="login.submit">Sign in</button>
</form>
