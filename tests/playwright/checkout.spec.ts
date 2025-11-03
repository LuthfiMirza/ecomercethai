import { test, expect } from '@playwright/test';

const CUSTOMER_EMAIL = process.env.E2E_CUSTOMER_EMAIL ?? 'customer@example.com';
const CUSTOMER_PASSWORD = process.env.E2E_CUSTOMER_PASSWORD ?? 'password';

test.describe('Checkout form focusability', () => {
  test('coupon and new address inputs accept focus and typing', async ({ page }) => {
    await page.goto('/en/login');

    await page.fill('input[name="email"]', CUSTOMER_EMAIL);
    await page.fill('input[name="password"]', CUSTOMER_PASSWORD);

    await Promise.all([
      page.waitForNavigation({ url: /\/en\/account/ }),
      page.click('button[type="submit"]'),
    ]);

    await page.goto('/en/checkout');

    const couponInput = page.getByPlaceholder('Masukkan kode');
    await couponInput.waitFor({ state: 'visible' });
    await couponInput.click();
    await couponInput.fill('');
    await couponInput.type('TEST10');
    await expect(couponInput).toHaveValue('TEST10');

    await page.getByRole('button', { name: /Add new address/i }).click();
    const addressNameInput = page.locator('#new_address_name');
    await addressNameInput.waitFor({ state: 'visible' });
    await addressNameInput.click();
    await addressNameInput.fill('');
    await addressNameInput.type('Focus Check');
    await expect(addressNameInput).toHaveValue('Focus Check');
  });
});
