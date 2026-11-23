<?php

function selectPaymentProvider(string $providerName): PaymentProvider
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
