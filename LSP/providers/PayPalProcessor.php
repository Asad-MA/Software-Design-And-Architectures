<?php

class PayPalPaymentProcessor implements PaymentProvider
{
    public function processPayment(float $amount): PaymentOutcome
    {
        //simulate balance
        $balance = 1000.00;


        // Simulate payment processing logic for PayPal
        if ($amount <= 0) {
            return PaymentResult::failure('Invalid payment amount.');
        }

        if ($amount > $balance) {
            return PaymentResult::failure('Insufficient funds in your PayPal account.');
        }

        $transaction_id = 'trnx_paypal' . microtime();

        // Simulate a successful payment
        return PaymentResult::success($transaction_id, 'Successful payment processed via PayPal.');
    }
}