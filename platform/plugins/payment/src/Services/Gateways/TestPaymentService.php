<?php

namespace Botble\Payment\Services\Gateways;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;

class TestPaymentService
{
    public function execute(array $paymentData): string
    {
        // Generate a fake charge ID for testing
        $chargeId = 'test_' . uniqid();
        
        // Store the payment locally
        PaymentHelper::storeLocalPayment([
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'],
            'charge_id' => $chargeId,
            'order_id' => $paymentData['order_id'],
            'customer_id' => $paymentData['customer_id'] ?? null,
            'customer_type' => $paymentData['customer_type'] ?? null,
            'payment_channel' => 'test',
            'status' => PaymentStatusEnum::COMPLETED, // Auto-complete for testing
        ]);
        
        return $chargeId;
    }
    
    public function makePayment(Request $request)
    {
        // For testing, we'll just return success
        return true;
    }
    
    public function afterMakePayment(Request $request)
    {
        // Nothing needed for test payments
    }
    
    public function supportedCurrencyCodes(): array
    {
        return ['USD', 'EUR', 'GBP']; // Support common currencies for testing
    }
}
