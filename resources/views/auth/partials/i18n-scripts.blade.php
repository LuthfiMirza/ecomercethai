@once
    <script>
        (function(){
            const dictionary = {
                en: {
                    'login.heading': 'Welcome back',
                    'login.subtitle': 'Sign in to continue your shopping journey.',
                    'login.highlights.fast_checkout': 'Save your favorites and check out faster.',
                    'login.highlights.order_tracking': 'Track orders anytime from your dashboard.',
                    'login.highlights.wishlist_sync': 'Sync wishlist items across your devices.',
                    'login.email_label': 'Email address',
                    'login.email_placeholder': 'Enter your email',
                    'login.password_label': 'Password',
                    'login.password_placeholder': 'Enter your password',
                    'login.remember_me': 'Remember me',
                    'login.forgot_password': 'Forgot password?',
                    'login.submit': 'Sign in',
                    'login.no_account': 'Don’t have an account?',
                    'login.no_account_cta': 'Create one',
                    'login.social_divider': 'Or continue with',
                    'login.badge_secure': 'Secure login',

                    'register.heading': 'Create your account',
                    'register.subtitle': 'Join our community and unlock member-only deals.',
                    'register.highlights.reward': 'Get exclusive promotions and launch alerts.',
                    'register.highlights.wishlist': 'Keep wishlist items synced everywhere.',
                    'register.highlights.checkout': 'Check out quickly with saved details.',
                    'register.name_label': 'Full name',
                    'register.name_placeholder': 'Enter your full name',
                    'register.email_label': 'Email address',
                    'register.email_placeholder': 'Enter your email',
                    'register.password_label': 'Password',
                    'register.password_placeholder': 'Create a strong password',
                    'register.password_confirmation_label': 'Confirm password',
                    'register.password_confirmation_placeholder': 'Repeat your password',
                    'register.submit': 'Sign up',
                    'register.have_account': 'Already have an account?',
                    'register.have_account_cta': 'Sign in',
                    'register.social_divider': 'Or sign up with',
                    'register.badge': 'New here?',

                    'account.greeting_prefix': 'Hello',
                    'account.open_cart': 'Open cart',
                    'account.open_wishlist': 'View wishlist',
                    'account.settings_heading': 'Account settings',
                    'account.settings_description': 'Manage your sessions and keep your account secure.',
                    'account.logout': 'Logout',
                    'account.login_block_title': 'Sign in',
                    'account.login_block_subtitle': 'Access your cart, wishlist, and order history.',
                    'account.register_block_title': 'Create account',
                    'account.register_block_subtitle': 'Set up a new account for a more personalised experience.',
                },
                th: {
                    'login.heading': 'ยินดีต้อนรับกลับมา',
                    'login.subtitle': 'เข้าสู่ระบบเพื่อช้อปปิ้งต่ออย่างราบรื่น',
                    'login.highlights.fast_checkout': 'บันทึกรายการโปรดและชำระเงินได้รวดเร็วขึ้น',
                    'login.highlights.order_tracking': 'ติดตามคำสั่งซื้อได้ทุกเมื่อจากแดชบอร์ดของคุณ',
                    'login.highlights.wishlist_sync': 'ซิงค์รายการโปรดข้ามอุปกรณ์ทั้งหมดของคุณ',
                    'login.email_label': 'อีเมล',
                    'login.email_placeholder': 'กรอกอีเมลของคุณ',
                    'login.password_label': 'รหัสผ่าน',
                    'login.password_placeholder': 'กรอกรหัสผ่านของคุณ',
                    'login.remember_me': 'จดจำฉัน',
                    'login.forgot_password': 'ลืมรหัสผ่าน?',
                    'login.submit': 'เข้าสู่ระบบ',
                    'login.no_account': 'ยังไม่มีบัญชี?',
                    'login.no_account_cta': 'สมัครสมาชิก',
                    'login.social_divider': 'หรือเข้าสู่ระบบด้วยบัญชีอื่น',
                    'login.badge_secure': 'เข้าสู่ระบบอย่างปลอดภัย',

                    'register.heading': 'สร้างบัญชีของคุณ',
                    'register.subtitle': 'เข้าร่วมชุมชนของเราและรับดีลพิเศษสำหรับสมาชิกเท่านั้น',
                    'register.highlights.reward': 'รับโปรโมชันพิเศษและข่าวเปิดตัวสินค้าใหม่',
                    'register.highlights.wishlist': 'เก็บรายการโปรดไว้ให้ซิงค์ได้ทุกอุปกรณ์',
                    'register.highlights.checkout': 'ชำระเงินได้รวดเร็วด้วยข้อมูลที่บันทึกไว้',
                    'register.name_label': 'ชื่อเต็ม',
                    'register.name_placeholder': 'กรอกชื่อ-นามสกุลของคุณ',
                    'register.email_label': 'อีเมล',
                    'register.email_placeholder': 'กรอกอีเมลของคุณ',
                    'register.password_label': 'รหัสผ่าน',
                    'register.password_placeholder': 'สร้างรหัสผ่านที่ปลอดภัย',
                    'register.password_confirmation_label': 'ยืนยันรหัสผ่าน',
                    'register.password_confirmation_placeholder': 'กรอกรหัสผ่านอีกครั้ง',
                    'register.submit': 'สมัครสมาชิก',
                    'register.have_account': 'มีบัญชีอยู่แล้ว?',
                    'register.have_account_cta': 'เข้าสู่ระบบ',
                    'register.social_divider': 'หรือสมัครสมาชิกด้วยบัญชีอื่น',
                    'register.badge': 'สมาชิกใหม่',

                    'account.greeting_prefix': 'สวัสดี',
                    'account.open_cart': 'เปิดตะกร้าสินค้า',
                    'account.open_wishlist': 'ดูรายการโปรด',
                    'account.settings_heading': 'การตั้งค่าบัญชี',
                    'account.settings_description': 'จัดการการเข้าสู่ระบบและดูแลความปลอดภัยของบัญชีคุณ',
                    'account.logout': 'ออกจากระบบ',
                    'account.login_block_title': 'เข้าสู่ระบบ',
                    'account.login_block_subtitle': 'เข้าถึงตะกร้า รายการโปรด และประวัติคำสั่งซื้อของคุณ',
                    'account.register_block_title': 'สร้างบัญชี',
                    'account.register_block_subtitle': 'เริ่มต้นบัญชีใหม่เพื่อประสบการณ์ที่เป็นส่วนตัวมากขึ้น',
                }
            };

            const applyTranslations = (lang) => {
                const locales = dictionary[lang] || dictionary.en;
                document.querySelectorAll('[data-i18n]').forEach((el) => {
                    const key = el.dataset.i18n;
                    if (!key) return;
                    const value = locales[key] ?? dictionary.en[key];
                    if (typeof value === 'string') {
                        if (el.dataset.i18nType === 'html') {
                            el.innerHTML = value;
                        } else {
                            el.textContent = value;
                        }
                    }
                });
                document.querySelectorAll('[data-i18n-placeholder]').forEach((el) => {
                    const key = el.dataset.i18nPlaceholder;
                    if (!key) return;
                    const value = locales[key] ?? dictionary.en[key];
                    if (typeof value === 'string') {
                        el.setAttribute('placeholder', value);
                    }
                });
            };

            const currentLang = (window.App && window.App.lang) || localStorage.getItem('lang') || 'en';
            applyTranslations(currentLang);

            document.addEventListener('i18n:change', (event) => {
                const nextLang = event.detail?.lang || 'en';
                if (window.App) {
                    window.App.lang = nextLang;
                }
                applyTranslations(nextLang);
            });
        })();
    </script>
@endonce
