<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZarinPal Merchant ID
    |--------------------------------------------------------------------------
    |
    | This is your merchant ID from ZarinPal dashboard. For sandbox testing,
    | you can use any 36-character string (GUID format).
    |
    */
    'merchant_id' => env('ZARINPAL_MERCHANT_ID', '00000000-0000-0000-0000-000000000000'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | This option controls whether to use ZarinPal's sandbox environment for
    | testing purposes. Set to true for sandbox mode, false for production.
    |
    */
    'sandbox' => env('ZARINPAL_SANDBOX', false),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency for your payments. It can be 'IRT' for Toman
    | or 'IRR' for Rial. The default value is 'IRT' (Toman).
    |
    */
    'currency' => env('ZARINPAL_CURRENCY', 'IRT'),

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    |
    | This option enables logging for all ZarinPal related operations.
    | Logs will be stored in your Laravel log files.
    |
    */
    'enable_logging' => env('ZARINPAL_LOGGING', true),
];