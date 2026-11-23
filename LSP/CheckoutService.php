<?php

final class CheckoutService
{


    public function checkout(PaymentProvider $paymentProvider, float $amount): array
    {
        $transaction = $paymentProvider->processPayment($amount, 'USD');



        return array(
            'amount' => $amount,
            'transaction_id' => $transaction->isSuccess() ? $this->generateTransactionId() : null,
            'status' => $transaction->getStatus(),
            'message' => $transaction->getMessage()
        );
    }

    public static function selectPaymentProvider(string $providerName): PaymentProvider
    {
        switch ($providerName) {
            case 'paypal':
                return new PayPalPaymentProvider();
            case 'bank':
                return new BankTransferProcessor();
            case 'card':
                return new CreditCardProcessor();
            default:
                throw new InvalidArgumentException("Invalid payment provider: $providerName");
        }
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
