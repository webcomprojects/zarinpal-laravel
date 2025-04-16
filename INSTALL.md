# ZarinPal Laravel Payment Gateway - Installation Guide

این راهنمای نصب و پیاده‌سازی درگاه پرداخت زرین‌پال در پروژه‌های لاراول است، که قابلیت استفاده از sandbox را نیز پشتیبانی می‌کند.

## 1. ایجاد فایل‌های مورد نیاز

ابتدا فایل‌های زیر را در مسیرهای مشخص شده در پروژه لاراول خود ایجاد کنید:

### ساختار فایل‌ها

```
app/
├── Services/
│   └── ZarinPal/
│       └── ZarinPalService.php
├── Providers/
│   └── ZarinPalServiceProvider.php
├── Facades/
│   └── ZarinPal.php
├── Http/
│   └── Controllers/
│       └── PaymentController.php
├── Models/
│   └── Order.php
config/
└── zarinpal.php
resources/
└── views/
    └── checkout/
        ├── index.blade.php
        ├── success.blade.php
        └── failed.blade.php
database/
└── migrations/
    └── yyyy_mm_dd_create_orders_table.php
```

## 2. ثبت سرویس پروایدر

در فایل `config/app.php` سرویس پروایدر را به آرایه `providers` اضافه کنید:

```php
'providers' => [
    // ...
    App\Providers\ZarinPalServiceProvider::class,
],
```

## 3. ثبت فساد (Facade)

در فایل `config/app.php` فساد را به آرایه `aliases` اضافه کنید:

```php
'aliases' => [
    // ...
    'ZarinPal' => App\Facades\ZarinPal::class,
],
```

## 4. تنظیمات محیطی (.env)

متغیرهای زیر را به فایل `.env` خود اضافه کنید:

```
ZARINPAL_MERCHANT_ID=your-merchant-id
ZARINPAL_SANDBOX=true
ZARINPAL_CURRENCY=IRT
ZARINPAL_LOGGING=true
```

برای تست در محیط sandbox، به جای `your-merchant-id` می‌توانید از هر رشته 36 کاراکتری با فرمت GUID استفاده کنید.

## 5. اجرای مایگریشن‌ها

مایگریشن جدید برای جدول سفارش‌ها را اجرا کنید:

```bash
php artisan migrate
```

## 6. مسیرهای درگاه پرداخت

مسیرهای زیر را به فایل `routes/web.php` اضافه کنید:

```php
// Payment process routes
Route::middleware(['auth'])->group(function () {
    // Initialize payment and redirect to ZarinPal
    Route::get('/payment/pay/{orderId}', [PaymentController::class, 'pay'])
        ->name('payment.pay');
});

// Payment callback route (doesn't require auth as the user comes back from ZarinPal)
Route::get('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

// Success and failure routes
Route::get('/checkout/success', function () {
    return view('checkout.success');
})->name('checkout.success');

Route::get('/checkout/failed', function () {
    return view('checkout.failed');
})->name('checkout.failed');
```

## 7. نحوه استفاده

### مثال ساده با استفاده از فساد (Facade)

```php
use App\Facades\ZarinPal;

// درخواست پرداخت
$result = ZarinPal::request(
    10000,                           // مبلغ به تومان (یا ریال بر اساس تنظیمات)
    route('payment.callback'),       // آدرس بازگشت
    'پرداخت سفارش #123',            // توضیحات
    'customer@example.com',          // ایمیل مشتری (اختیاری)
    '09123456789'                    // موبایل مشتری (اختیاری)
);

if ($result['success']) {
    // ذخیره شناسه پرداخت برای تأیید بعدی
    $authority = $result['authority'];
    
    // هدایت کاربر به صفحه پرداخت زرین‌پال
    return redirect($result['paymentUrl']);
} else {
    // مدیریت خطا
    return 'خطا: ' . $result['error']['message'];
}

// در مسیر بازگشت
public function verify(Request $request)
{
    $authority = $request->input('Authority');
    $status = $request->input('Status');
    
    if ($status !== 'OK') {
        return 'پرداخت توسط کاربر لغو شد.';
    }
    
    // تأیید پرداخت
    $result = ZarinPal::verify($authority, 10000); // مبلغ باید با مبلغ درخواست یکسان باشد
    
    if ($result['success']) {
        $referenceId = $result['referenceId'];
        return 'پرداخت با موفقیت انجام شد. کد پیگیری: ' . $referenceId;
    } else {
        return 'خطا در تأیید پرداخت: ' . $result['error']['message'];
    }
}
```

### تست در محیط Sandbox

برای استفاده از محیط sandbox زرین‌پال برای تست:

1. مقدار `ZARINPAL_SANDBOX=true` را در فایل `.env` تنظیم کنید
2. از هر رشته 36 کاراکتری با فرمت GUID به عنوان merchant ID استفاده کنید
3. هنگام تست پرداخت، سیستم هیچ پولی واقعی دریافت نمی‌کند

طبق مستندات زرین‌پال، آدرس‌های sandbox عبارتند از:
- آدرس درخواست: `https://sandbox.zarinpal.com/pg/v4/payment/request.json`
- آدرس تأیید: `https://sandbox.zarinpal.com/pg/v4/payment/verify.json`
- آدرس پرداخت: `https://sandbox.zarinpal.com/pg/StartPay/`

## 8. مدیریت خطاها

پکیج شامل مدیریت خطای جامع است. زمانی که درخواست یا تأیید با خطا مواجه می‌شود، آرایه برگشتی شامل موارد زیر خواهد بود:

```php
[
    'success' => false,
    'error' => [
        'code' => 'ERROR_CODE',
        'message' => 'توضیحات خطا'
    ]
]
```

تمام استثناها نیز در صورت فعال بودن لاگ در تنظیمات، ثبت می‌شوند.

## 9. ویژگی‌های اصلی پکیج

- پشتیبانی از محیط sandbox برای تست
- قابلیت تنظیم merchant ID به صورت پویا برای هر درخواست
- پشتیبانی از واحد پولی تومان و ریال
- مدیریت خطای جامع
- ثبت لاگ برای تراکنش‌ها
- سهولت استفاده با Facade
- سازگار با نسخه REST API زرین‌پال