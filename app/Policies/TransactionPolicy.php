<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Marketplace\Models\Transaction;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction): bool
    {
        return $transaction->buyer_id === $user->id || $transaction->seller_id === $user->id;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $transaction->seller_id === $user->id && $transaction->status === 'pending';
    }
}
