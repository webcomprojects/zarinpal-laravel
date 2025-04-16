<?php

namespace App\Services\ZarinPal;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZarinPalService
{
    /**
     * ZarinPal merchant ID
     * @var string
     */
    protected $merchantId;

    /**
     * Determines whether to use sandbox mode
     * @var bool
     */
    protected $sandbox;

    /**
     * API endpoints for ZarinPal
     * @var array
     */
    protected $endpoints = [
        'production' => [
            'request' => 'https://api.zarinpal.com/pg/v4/payment/request.json',
            'verify' => 'https://api.zarinpal.com/pg/v4/payment/verify.json',
            'startPay' => 'https://www.zarinpal.com/pg/StartPay/',
        ],
        'sandbox' => [
            'request' => 'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            'verify' => 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            'startPay' => 'https://sandbox.zarinpal.com/pg/StartPay/',
        ],
    ];

    /**
     * ZarinPalService constructor.
     * 
     * @param string|null $merchantId
     * @param bool|null $sandbox
     */
    public function __construct(string $merchantId = null, bool $sandbox = null)
    {
        $this->merchantId = $merchantId ?? config('zarinpal.merchant_id');
        $this->sandbox = $sandbox ?? config('zarinpal.sandbox', false);
    }

    /**
     * Set merchant ID dynamically
     * 
     * @param string $merchantId
     * @return $this
     */
    public function setMerchantId(string $merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * Set sandbox mode dynamically
     * 
     * @param bool $sandbox
     * @return $this
     */
    public function setSandbox(bool $sandbox)
    {
        $this->sandbox = $sandbox;
        return $this;
    }

    /**
     * Get the current environment's endpoints
     * 
     * @return array
     */
    protected function getEndpoints()
    {
        return $this->sandbox ? $this->endpoints['sandbox'] : $this->endpoints['production'];
    }

    /**
     * Request a payment from ZarinPal
     * 
     * @param int $amount Amount in Toman
     * @param string $callbackUrl URL to redirect after payment
     * @param string $description Payment description
     * @param string|null $email Customer email (optional)
     * @param string|null $mobile Customer mobile (optional)
     * @return array
     */
    public function request(int $amount, string $callbackUrl, string $description, string $email = null, string $mobile = null)
    {
        $endpoints = $this->getEndpoints();
        
        $data = [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'description' => $description,
            'callback_url' => $callbackUrl,
        ];

        // Add optional parameters if provided
        if ($email) {
            $data['metadata']['email'] = $email;
        }

        if ($mobile) {
            $data['metadata']['mobile'] = $mobile;
        }

        try {
            $response = Http::post($endpoints['request'], $data);
            $result = $response->json();

            if ($response->successful() && isset($result['data']) && isset($result['data']['authority'])) {
                return [
                    'success' => true,
                    'authority' => $result['data']['authority'],
                    'paymentUrl' => $endpoints['startPay'] . $result['data']['authority'],
                ];
            }

            $errorCode = $result['errors']['code'] ?? null;
            $errorMessage = $result['errors']['message'] ?? 'Unknown error';

            return [
                'success' => false,
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('ZarinPal payment request failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => [
                    'code' => 'EXCEPTION',
                    'message' => 'An exception occurred during payment request',
                ],
            ];
        }
    }

    /**
     * Verify a payment using authority and amount
     * 
     * @param string $authority Payment authority from callback
     * @param int $amount Amount in Toman
     * @return array
     */
    public function verify(string $authority, int $amount)
    {
        $endpoints = $this->getEndpoints();
        
        $data = [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'authority' => $authority,
        ];

        try {
            $response = Http::post($endpoints['verify'], $data);
            $result = $response->json();

            if ($response->successful() && isset($result['data']) && isset($result['data']['ref_id'])) {
                return [
                    'success' => true,
                    'referenceId' => $result['data']['ref_id'],
                    'cardHash' => $result['data']['card_hash'] ?? null,
                    'cardPan' => $result['data']['card_pan'] ?? null,
                ];
            }

            $errorCode = $result['errors']['code'] ?? null;
            $errorMessage = $result['errors']['message'] ?? 'Unknown error';

            return [
                'success' => false,
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('ZarinPal payment verification failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => [
                    'code' => 'EXCEPTION',
                    'message' => 'An exception occurred during payment verification',
                ],
            ];
        }
    }

    /**
     * Redirect user to payment gateway
     * 
     * @param string $authority Payment authority
     * @return string
     */
    public function getRedirectUrl(string $authority)
    {
        $endpoints = $this->getEndpoints();
        return $endpoints['startPay'] . $authority;
    }
}