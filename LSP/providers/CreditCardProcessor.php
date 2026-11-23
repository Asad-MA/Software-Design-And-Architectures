<?php

class CreditCardProcessor implements PaymentProvider
{
    public function processPayment(float $amount): PaymentOutcome
    {
        // Simulate Account Balance
        $balance = 500.00;

        // Simulate credit card payment processing logic
        if ($amount <= 0) {
            return PaymentResult::failure('Invalid payment amount.');
        }

        if ($amount > $balance) {
            return PaymentResult::failure('Insufficient funds in your credit card account.');
        }

        $transaction_id = 'trnx_card' . microtime();

        // Simulate successful payment processing
        return PaymentResult::success($transaction_id , 'Successful payment processed via Credit Card.');
    }
}