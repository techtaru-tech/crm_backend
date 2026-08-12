<?php

namespace App\Billing;

/**
 * Unified return type for PaymentGateway::checkout(). Keeps the caller
 * (BillingController) from having to know whether the active driver
 * wants to redirect, render instructions, or surface an error.
 */
class CheckoutResult
{
    public function __construct(
        public readonly string $type,       // 'redirect' | 'instructions' | 'error'
        public readonly ?string $url = null,
        public readonly ?string $message = null,
        public readonly array $data = [],
    ) {
    }

    public static function redirect(string $url): self
    {
        return new self('redirect', url: $url);
    }

    public static function instructions(string $message, array $data = []): self
    {
        return new self('instructions', message: $message, data: $data);
    }

    public static function error(string $message): self
    {
        return new self('error', message: $message);
    }

    public function isRedirect(): bool
    {
        return $this->type === 'redirect';
    }

    public function isInstructions(): bool
    {
        return $this->type === 'instructions';
    }

    public function isError(): bool
    {
        return $this->type === 'error';
    }
}
