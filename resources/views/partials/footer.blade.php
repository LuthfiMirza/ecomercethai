<footer class="bg-white border-t-2 border-[#FF7043] mt-12">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About Section -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('image/logo.jpg') }}" alt="{{ config('app.name', 'Lungpaeit') }}" class="h-10 w-10 rounded-full object-cover shadow-sm" loading="lazy">
                    <span class="text-2xl font-semibold text-gray-900">{{ config('app.name', 'Lungpaeit') }}</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">About Us</h3>
                <p class="text-gray-700">Your trusted partner for all computer and hardware needs in Thailand.</p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-700 hover:text-[#FF7043]">Computer Set</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-[#FF7043]">Hardware</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-[#FF7043]">Components</a></li>
                </ul>
            </div>
            
            <!-- Social Media -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Follow Us</h3>
                <div class="flex space-x-4 mb-6">
                    <a href="#" class="text-[#FF7043] hover:text-[#FF7043]/80"><i class="fab fa-facebook text-2xl"></i></a>
                    <a href="#" class="text-[#FF7043] hover:text-[#FF7043]/80"><i class="fab fa-instagram text-2xl"></i></a>
                    <a href="#" class="text-[#FF7043] hover:text-[#FF7043]/80"><i class="fab fa-youtube text-2xl"></i></a>
                    <a href="#" class="text-[#FF7043] hover:text-[#FF7043]/80"><i class="fab fa-tiktok text-2xl"></i></a>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-200 mt-8 pt-8 text-center">
            <p class="text-gray-600">&copy; {{ date('Y') }} {{ config('app.name', 'Lungpaeit') }}. All rights reserved.</p>
        </div>
    </div>
</footer>
