<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Loan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'term',
        'amount',
        'user_id',
        'status'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    protected $defaults = [
        'status' => 'PENDING'
    ];

    protected $appends = [
        'submit_date'
    ];

    public function users(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function getSubmitDateAttribute(): string
    {
        return $this->created_at;
    }

    public function scopeBelongToUser(Builder $query, string $userID): void
    {
        $query->where('user_id', $userID);
    }

    public function scopeIsExistWithID(Builder $query, string $loanID): void
    {
        $query->where('id', $loanID);
    }
}
