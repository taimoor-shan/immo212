<?php

use Botble\Base\Models\BaseModel;
use Botble\Payment\Models\Payment;
use Botble\Stripe\Supports\StripeHelper;

if (! function_exists('convert_stripe_amount_from_api')) {
    function convert_stripe_amount_from_api(float $amount, ?BaseModel $currency): float
    {
        return $amount / StripeHelper::getStripeCurrencyMultiplier($currency);
    }
}

if (! function_exists('get_payment_setting')) {
    function get_payment_setting(string $key, $type = null, $default = null): string|array|null
    {
        return setting(get_payment_setting_key($key, $type), $default);
    }
}

if (! function_exists('get_payment_setting_key')) {
    function get_payment_setting_key(string $key, ?string $type = null): string
    {
        $key = $type ? "payment_{$type}_{$key}" : "payment_$key";

        return apply_filters('payment_setting_key', $key);
    }
}

if (! function_exists('get_payment_is_support_refund_online')) {
    function get_payment_is_support_refund_online(Payment $payment): bool|string
    {
        $paymentService = $payment->payment_channel->getServiceClass();

        if (! $paymentService || ! class_exists($paymentService)) {
            return false;
        }

        if (! method_exists($paymentService, 'getSupportRefundOnline')) {
            return false;
        }

        try {
            $isSupportRefund = (new $paymentService())->getSupportRefundOnline();

            return $isSupportRefund ? $paymentService : false;
        } catch (Exception) {
            return false;
        }
    }
}

if (! function_exists('get_payment_methods')) {
    function get_payment_methods(): array
    {
        $methods = [];

        // Get all available payment method enums
        $availableMethods = \Botble\Payment\Enums\PaymentMethodEnum::values();

        foreach ($availableMethods as $method) {
            $methodValue = $method->getValue();
            $status = get_payment_setting('status', $methodValue, 0);
            $name = get_payment_setting('name', $methodValue) ?: $method->displayName();
            $description = get_payment_setting('description', $methodValue, '');

            // Enable test payment method in development mode
            if ($methodValue === 'test' && (app()->environment('local', 'development') || config('app.debug'))) {
                $status = 1;
                $name = $name ?: 'Test Payment (Development)';
                $description = $description ?: 'Test payment method for development purposes. No actual payment will be processed.';
            }

            $methods[$methodValue] = [
                'name' => $name,
                'description' => $description,
                'status' => (int) $status,
            ];
        }

        // Add methods from other payment plugins via filters
        $methods = apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS_DATA, $methods);

        return $methods;
    }
}
