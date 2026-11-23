<?php

final class PaymentResult{
    public function __construct(private bool $success, private string $transactionId, private ?string $message = null)
    {
    }

    public static function success(string $status = 'success/accepted', ?string $message = null): self
    {
        return new self(true, $status, $message);
    }

    public static function failure(string $status = 'failed/rejected', ?string $message = null): self
    {
        return new self(false, $status, $message);
    }

    public function getStatus(): string
    {
        return $this->status;
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