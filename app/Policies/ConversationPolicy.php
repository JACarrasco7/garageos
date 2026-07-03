<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Messaging\Models\Conversation;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function reply(User $user, Conversation $conversation): bool
    {
        return $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id;
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id;
    }
}
