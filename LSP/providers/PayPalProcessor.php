<?php

class PayPalPaymentProvider implements PaymentProvider
{
    public function processPayment(float $amount, string $currency): PaymentResult
    {
        // Simulate payment processing logic for PayPal
        if ($amount <= 0) {
            return PaymentResult::failure('failed/rejected', 'Invalid payment amount.');
        }

        // Simulate a successful payment
        return PaymentResult::success('success/accepted', 'Payment processed successfully via PayPal.');
    }
}