<?php

class CreditCardProcessor implements PaymentProvider
{
    public function processPayment(float $amount, string $currency): PaymentResult
    {
        // Simulate credit card payment processing logic
        if ($amount <= 0) {
            return new PaymentResult(false, 'failed/rejected', 'Invalid payment amount.');
        }

        // Simulate successful payment processing
        return new PaymentResult(true, 'success/accepted', 'Payment processed successfully via credit card.');
    }
}