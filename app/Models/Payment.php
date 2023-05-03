<?php

namespace App\Models;

use App\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $hidden = [
        'id',
        'loan_id',
        'created_at',
        'updated_at'
    ];

    public function loans() {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }
    public function scopeUnpaidBelongsToLoan(Builder $query, Loan $loan): void
    {
        $query->where('loan_id', $loan->id);
        $query->where('status', StatusEnum::PENDING);
    }
}
