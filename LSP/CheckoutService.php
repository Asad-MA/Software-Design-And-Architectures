<?php

final class CheckoutService
{
    public function __construct(private PaymentProvider $paymentProvider)
    {
        // No initialization required for this service
    }

    public function checkout(float $amount): array
    {
        $transaction = $this->paymentProvider->processPayment($amount, 'USD');



        return array(
            'amount' => $amount,
            'transaction_id' => $transaction->isSuccess() ? $this->generateTransactionId() : null,
            'status' => $transaction->getStatus(),
            'message' => $transaction->getMessage()
        );
    }


    public function generateTransactionId(string $prefix = 'txn_'): string
    {
        // Microsecond timestamp for chronological sorting
        $timestamp = (new DateTimeImmutable())->format('YmdHisv');

        // 8 bytes = 16 cryptographically secure hex characters
        $random = bin2hex(random_bytes(8));

        return $prefix . $timestamp . '_' . strtoupper($random);
    }
}
