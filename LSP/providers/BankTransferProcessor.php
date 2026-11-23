<?php

class BankTransferProcessor implements PaymentProvider
{
    public function processPayment(float $amount, string $currency): PaymentResult
    {
        // Simulate bank transfer payment processing logic
        if ($amount <= 0) {
            return PaymentResult::failure('failed/rejected', 'Invalid payment amount.');
        }

        // Simulate a successful payment
        return PaymentResult::success('success/accepted', 'Payment processed successfully via bank transfer.');
    }


}