<?php

namespace App\Http\Requests;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // DRAFT is the initial state only; nobody can transition *to* it.
            'status' => ['required', 'string', Rule::in([
                PurchaseOrderStatus::Approved->value,
                PurchaseOrderStatus::Received->value,
                PurchaseOrderStatus::Cancelled->value,
            ])],
        ];
    }

    public function targetStatus(): PurchaseOrderStatus
    {
        return PurchaseOrderStatus::from($this->validated('status'));
    }
}