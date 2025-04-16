# ZarinPal Payment Gateway for Laravel

This package provides a simple and reusable implementation of the ZarinPal payment gateway for Laravel applications, with support for sandbox testing mode.

## Installation

### 1. Copy the files to your project
Place the following files in your Laravel project:

- `app/Services/ZarinPal/ZarinPalService.php`
- `app/Providers/ZarinPalServiceProvider.php`
- `app/Facades/ZarinPal.php`
- `config/zarinpal.php`

### 2. Register the Service Provider
Add the service provider to the `providers` array in `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\ZarinPalServiceProvider::class,
],
```

### 3. Register the Facade
Add the facade to the `aliases` array in `config/app.php`:

```php
'aliases' => [
    // ...
    'ZarinPal' => App\Facades\ZarinPal::class,
],
```

### 4. Publish the configuration file (optional)
```bash
php artisan vendor:publish --tag=zarinpal-config
```

### 5. Configure your environment variables
Add the following to your `.env` file:

```
ZARINPAL_MERCHANT_ID=your-merchant-id
ZARINPAL_SANDBOX=true  # Set to false for production
ZARINPAL_CURRENCY=IRT  # IRT for Toman, IRR for Rial
ZARINPAL_LOGGING=true
```

## Usage

### Basic Usage with Facade

```php
use App\Facades\ZarinPal;

// Request a payment
$result = ZarinPal::request(
    10000,                        // Amount in Toman (or Rial based on config)
    'https://yoursite.com/verify', // Callback URL
    'Payment for Order #123',     // Description
    'customer@example.com',       // Customer email (optional)
    '09123456789'                 // Customer mobile (optional)
);

if ($result['success']) {
    // Store the authority for verification later
    $authority = $result['authority'];
    
    // Redirect the user to ZarinPal payment page
    return redirect($result['paymentUrl']);
} else {
    // Handle error
    return 'Error: ' . $result['error']['message'];
}

// In your callback route
public function verify(Request $request)
{
    $authority = $request->input('Authority');
    $status = $request->input('Status');
    
    if ($status !== 'OK') {
        return 'Payment was canceled by user.';
    }
    
    // Verify the payment
    $result = ZarinPal::verify($authority, 10000); // Amount must be the same as request
    
    if ($result['success']) {
        $referenceId = $result['referenceId'];
        return 'Payment was successful. Reference ID: ' . $referenceId;
    } else {
        return 'Error in payment verification: ' . $result['error']['message'];
    }
}
```

### Dependency Injection

You can also use dependency injection in your controllers:

```php
use App\Services\ZarinPal\ZarinPalService;

class PaymentController extends Controller
{
    protected $zarinpal;
    
    public function __construct(ZarinPalService $zarinpal)
    {
        $this->zarinpal = $zarinpal;
    }
    
    public function pay()
    {
        $result = $this->zarinpal->request(
            10000,
            route('payment.callback'),
            'Payment for Order #123'
        );
        
        // Rest of your code...
    }
}
```

### Setting Merchant ID or Sandbox Mode Dynamically

You can override the merchant ID or sandbox setting for specific requests:

```php
use App\Facades\ZarinPal;

// Set merchant ID for a specific payment
$result = ZarinPal::setMerchantId('another-merchant-id')
    ->request(10000, $callbackUrl, $description);

// Enable sandbox for a specific payment
$result = ZarinPal::setSandbox(true)
    ->request(10000, $callbackUrl, $description);
```

## Sandbox Testing

To use the ZarinPal sandbox for testing:

1. Set `ZARINPAL_SANDBOX=true` in your `.env` file
2. Use any 36-character string (GUID format) as your merchant ID
3. When making test payments, the system will not charge any real money

According to ZarinPal's documentation, the sandbox URLs are:
- Request URL: `https://sandbox.zarinpal.com/pg/v4/payment/request.json`
- Verify URL: `https://sandbox.zarinpal.com/pg/v4/payment/verify.json`
- Payment URL: `https://sandbox.zarinpal.com/pg/StartPay/`

## Error Handling

The package includes comprehensive error handling. When a request or verification fails, the returned array will include:

```php
[
    'success' => false,
    'error' => [
        'code' => 'ERROR_CODE',
        'message' => 'Error message description'
    ]
]
```

All exceptions are also logged if logging is enabled in the configuration.

## License

This package is open-sourced software licensed under the MIT license.