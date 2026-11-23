<?php

class BankTransferProcessor implements PaymentProvider
{
    public function processPayment(float $amount): PaymentOutcome
    {
        //Simulate Bank Transfer Balance
        $balance = 2000.00;

        // Simulate bank transfer payment processing logic
        if ($amount <= 0) {
            return PaymentResult::failure('Invalid payment amount.');
        }

        if ($amount > $balance) {
            return PaymentResult::failure('Insufficient funds in your bank account.');
        }

        $transaction_id = 'trnx_bank' . microtime();

        // Simulate successful payment processing
        return PaymentResult::success($transaction_id , 'Successful payment processed via Bank Transfer.');
    }


}