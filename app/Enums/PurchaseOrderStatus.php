<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'DRAFT';
    case Approved = 'APPROVED';
    case Received = 'RECEIVED';
    case Cancelled = 'CANCELLED';

    /** @return array<int, self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Approved, self::Cancelled],
            self::Approved => [self::Received, self::Cancelled],
            self::Received, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    /** RECEIVED and CANCELLED orders are immutable. */
    public function isFinal(): bool
    {
        return $this === self::Received || $this === self::Cancelled;
    }

    public function badge(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Approved => 'primary',
            self::Received => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function actionLabel(): string
    {
        return match ($this) {
            self::Approved => 'Approve',
            self::Received => 'Mark as received',
            self::Cancelled => 'Cancel order',
            self::Draft => 'Back to draft',
        };
    }
}