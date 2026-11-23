<?php

class BankTransferProcessor implements PaymentProvider
{
    public function processPayment(float $amount, string $currency): PaymentResult
    {
        // Simulate bank transfer payment processing logic
        if ($amount <= 0) {
            return new PaymentResult(false, 'failed/rejected', 'Invalid payment amount.');
        }

        // Simulate a successful payment
        return new PaymentResult(true, 'success/accepted', 'Payment processed successfully via bank transfer.');
    }
}