<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\ZarinPal\ZarinPalService setMerchantId(string $merchantId)
 * @method static \App\Services\ZarinPal\ZarinPalService setSandbox(bool $sandbox)
 * @method static array request(int $amount, string $callbackUrl, string $description, string $email = null, string $mobile = null)
 * @method static array verify(string $authority, int $amount)
 * @method static string getRedirectUrl(string $authority)
 * 
 * @see \App\Services\ZarinPal\ZarinPalService
 */
class ZarinPal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zarinpal';
    }
}