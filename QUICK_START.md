# Quick Start Guide - Toko Thailand

## 🚀 Getting Started

### 1. Install Dependencies
```bash
cd "/Applications/XAMPP/xamppfiles/htdocs/thai projek/toko-thailand"
composer install
npm install
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Create Admin User
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@tokothailand.com',
    'password' => bcrypt('password'),
]);

$role = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
$user->assignRole('admin');
exit
```

### 4. Configure Mail (Optional)
Update `.env`:
```env
MAIL_MAILER=log
# or use real SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### 5. Build Assets
```bash
npm run dev
# or for production
npm run build
```

### 6. Start Server
```bash
php artisan serve
```

Visit: http://localhost:8000

## 🧪 Run Tests
```bash
php artisan test
```

## 📋 Key Routes

### Public Routes
- `/` - Home page
- `/catalog` - Product catalog with filters
- `/product/{slug}` - Product detail
- `/contact` - Contact form
- `/login` - User login
- `/register` - User registration

### Authenticated User Routes
- `/cart` - Shopping cart
- `/wishlist` - Wishlist
- `/checkout` - Checkout page
- `/orders/{id}` - Order detail
- `/account` - User account

### Admin Routes (requires 'admin' role)
- `/admin/dashboard` - Admin dashboard
- `/admin/products` - Product management
- `/admin/categories` - Category management
- `/admin/orders` - Order management
- `/admin/users` - User management
- `/admin/promos` - Promo/coupon management
- `/admin/reports` - Reports

## 🔑 API Endpoints

### Cart
- `POST /cart/add` - Add product to cart
- `PUT /cart/{id}` - Update cart item quantity
- `DELETE /cart/{id}` - Remove item from cart
- `DELETE /cart` - Clear cart

### Wishlist
- `POST /wishlist/add` - Add to wishlist
- `DELETE /wishlist/{id}` - Remove from wishlist
- `DELETE /wishlist` - Clear wishlist

### Checkout
- `POST /checkout` - Process checkout
- `POST /checkout/apply-coupon` - Apply coupon code

### Payment
- `POST /payment/bank-transfer/{order}/upload` - Upload payment proof

## 🎨 Using Translations

In Controllers:
```php
return back()->with('success', __('cart.added'));
```

In Blade Views:
```blade
{{ __('checkout.empty_cart') }}
```

Available translation keys:
- `cart.*` - Cart messages
- `wishlist.*` - Wishlist messages
- `checkout.*` - Checkout messages
- `contact.*` - Contact form messages
- `payment.*` - Payment messages
- `mail.*` - Email subjects

## 💰 Currency Formatting

```blade
{{ format_price($product->price) }}
<!-- Output: ฿1,000.00 -->

{{ money($amount, 'THB') }}
<!-- Output: ฿1,000.00 (locale-aware) -->
```

## 🛒 Cart Usage Example

### Add to Cart (JavaScript)
```javascript
async function addToCart(productId, quantity) {
    const response = await fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
    });
    
    const data = await response.json();
    if (data.success) {
        alert(data.message);
        // Update cart count in UI
    }
}
```

## 📧 Sending Emails

### Order Confirmation
```php
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
```

### Order Status Update
```php
use App\Mail\OrderStatusUpdateMail;
use Illuminate\Support\Facades\Mail;

Mail::to($order->user->email)->send(new OrderStatusUpdateMail($order, 'shipped'));
```

## 🔒 Security Features

### Rate Limiting
- Login: 5 attempts per minute
- Contact form: 3 submissions per hour per IP

### Role-Based Access
```php
// Check if user has admin role
if (auth()->user()->hasRole('admin')) {
    // Admin only code
}

// In routes (already implemented)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});
```

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test
```bash
php artisan test --filter AdminProtectionTest
php artisan test --filter CartTest
php artisan test --filter CheckoutTest
```

### Create New Test
```bash
php artisan make:test ProductTest
```

## 📦 Database Seeding

### Create Seeder
```bash
php artisan make:seeder ProductSeeder
```

### Run Seeders
```bash
php artisan db:seed
# or specific seeder
php artisan db:seed --class=ProductSeeder
```

## 🔧 Common Tasks

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Generate IDE Helper (for better autocomplete)
```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models
```

### Create New Controller
```bash
php artisan make:controller ProductController
```

### Create New Model with Migration
```bash
php artisan make:model Product -m
```

### Create New Migration
```bash
php artisan make:migration add_field_to_table
```

## 📝 Environment Variables

Key variables in `.env`:
```env
APP_NAME="Toko Thailand"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_CURRENCY=THB

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toko_thailand
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@tokothailand.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🐛 Troubleshooting

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
```

### Composer Issues
```bash
composer dump-autoload
```

### Migration Issues
```bash
php artisan migrate:fresh
# Warning: This will drop all tables!
```

### Asset Issues
```bash
npm run build
php artisan storage:link
```

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev)

## 💡 Tips

1. Always use `__()` for translatable strings
2. Use `format_price()` for displaying prices
3. Validate all user inputs
4. Use database transactions for critical operations
5. Send emails asynchronously using queues (optional)
6. Keep controllers thin, move business logic to services
7. Write tests for critical features
8. Use factories for test data
9. Keep `.env` file secure and never commit it
10. Use rate limiting for sensitive endpoints

## 🎯 Next Features to Implement

1. Product reviews and ratings
2. Advanced search with Elasticsearch
3. Real-time notifications with Pusher
4. Social media login (OAuth)
5. Product recommendations
6. Inventory management
7. Multi-vendor support
8. Advanced analytics dashboard
9. Mobile app API
10. PWA support
