<?php

namespace App\Http\Controllers;

use App\Facades\ZarinPal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initialize payment and redirect to ZarinPal
     *
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pay($orderId)
    {
        // Find the order
        $order = Order::findOrFail($orderId);
        
        // Set the callback URL
        $callbackUrl = route('payment.callback');
        
        // Request payment from ZarinPal
        $result = ZarinPal::request(
            $order->total_amount,
            $callbackUrl,
            'پرداخت سفارش #' . $order->id,
            $order->customer_email,
            $order->customer_phone
        );
        
        if ($result['success']) {
            // Save authority to order for verification later
            $order->update([
                'payment_authority' => $result['authority'],
                'payment_status' => 'pending'
            ]);
            
            // Redirect to ZarinPal payment page
            return redirect($result['paymentUrl']);
        }
        
        // If payment request failed
        return redirect()->route('checkout.failed')->with(
            'error', 
            'خطا در اتصال به درگاه پرداخت: ' . ($result['error']['message'] ?? 'خطای نامشخص')
        );
    }
    
    /**
     * Verify payment after user is redirected back from ZarinPal
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        // Get Authority from the callback request
        $authority = $request->input('Authority');
        
        // Get Status from the callback request
        $status = $request->input('Status');
        
        // If payment was canceled by user
        if ($status !== 'OK') {
            return redirect()->route('checkout.failed')->with(
                'error', 
                'پرداخت توسط کاربر لغو شد'
            );
        }
        
        // Find the order by authority
        $order = Order::where('payment_authority', $authority)->first();
        
        if (!$order) {
            return redirect()->route('checkout.failed')->with(
                'error', 
                'سفارش مورد نظر یافت نشد'
            );
        }
        
        // Verify payment with ZarinPal
        $result = ZarinPal::verify($authority, $order->total_amount);
        
        if ($result['success']) {
            // Update order with successful payment info
            $order->update([
                'payment_status' => 'paid',
                'payment_reference_id' => $result['referenceId'],
                'payment_card_pan' => $result['cardPan'] ?? null,
                'payment_verified_at' => now(),
            ]);
            
            // Redirect to success page
            return redirect()->route('checkout.success')->with(
                'success', 
                'پرداخت با موفقیت انجام شد. شماره پیگیری: ' . $result['referenceId']
            );
        }
        
        // If verification failed
        $order->update(['payment_status' => 'failed']);
        
        return redirect()->route('checkout.failed')->with(
            'error', 
            'خطا در تایید پرداخت: ' . ($result['error']['message'] ?? 'خطای نامشخص')
        );
    }
}