<?php

final class CheckoutService
{
    public function __construct(private PaymentProvider $paymentProvider)
    {
        // No initialization required for this service
    }

    public function checkout(float $amount): PaymentOutcome
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        $transaction = $this->paymentProvider->processPayment($amount);



       return $transaction;
    }
}
