<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{

    /**
     * @param PaymentCollection $payments
     * @return array
     */
    public function toArrayWithPayment(PaymentCollection $payments): array
    {
        return [
            'status' => $this->status,
            'term' => $this->term,
            'amount' => $this->amount,
            'balanced' => $this->balanced,
            'submit_date' => $this->submit_date,
            'payments' => $payments->collection
        ];
    }
}
