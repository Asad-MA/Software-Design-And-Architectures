<?php

final class PaymentResult{
    public function __construct(private bool $success, private ?string $message = null)
    {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}

interface PaymentProvider
{
    public function processPayment(float $amount , string $currency ): PaymentResult;
}