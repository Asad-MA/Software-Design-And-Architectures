<?php

interface PaymentOutcome
{
    public function getTransactionID(): ?string;
    public function isSuccess(): bool;
    public function getMessage(): ?string;
    public function getStatus(): string;
}

final class PaymentResult implements PaymentOutcome
{
    private function __construct(private string $status, private string|null $transactionId, private ?string $message = null) {}

    public static function success(
        string $transactionId , string|null $message
    ): self {
        if (trim($transactionId) === '') {
            throw new InvalidArgumentException(
                'Transaction ID is required.'
            );
        }


        return new self('accepted/success', $transactionId, $message);
    }

    public static function failure(string $message): self
    {
        if (trim($message) === '') {
            throw new InvalidArgumentException(
                'Failure message is required.'
            );
        }

        return new self('failed/rejected', null, $message);
    }

    public function getTransactionID(): string|null
    {
        return $this->transactionId;
    }

    public function isSuccess(): bool
    {
        return $this->status === 'accepted/success';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}

interface PaymentProvider
{
    public function processPayment(float $amount): PaymentOutcome;
}
