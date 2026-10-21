<?php

namespace App\Exceptions;

use App\Enums\PurchaseOrderStatus;
use DomainException;
use Illuminate\Http\Request;

class InvalidStatusTransitionException extends DomainException
{
    public static function between(PurchaseOrderStatus $from, PurchaseOrderStatus $to): self
    {
        return new self("Cannot change a {$from->value} order to {$to->value}.");
    }

    public static function immutable(PurchaseOrderStatus $status): self
    {
        return new self("A {$status->value} order can no longer be modified.");
    }

    /** Domain errors surface as 422 for API clients and as a flash message on the web. */
    public function render(Request $request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => $this->getMessage(),
                'errors' => ['status' => [$this->getMessage()]],
            ], 422);
        }

        return back()->with('error', $this->getMessage());
    }

    public function report(): bool
    {
        return false; // expected business error, don't log
    }
}