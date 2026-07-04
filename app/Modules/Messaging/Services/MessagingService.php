<?php

namespace App\Modules\Messaging\Services;

use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;

class MessagingService
{
    /**
     * Create a new conversation or return existing one.
     */
    public function getOrCreateConversation(
        int $buyerId,
        int $sellerId,
        int $listingId,
        string $subject
    ): Conversation {
        $conversation = Conversation::where('listing_id', $listingId)
            ->where(function ($q) use ($buyerId, $sellerId) {
                $q->where('buyer_id', $buyerId)->where('seller_id', $sellerId)
                    ->orWhere('buyer_id', $sellerId)->where('seller_id', $buyerId);
            })
            ->first();

        if ($conversation) {
            return $conversation;
        }

        return Conversation::create([
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'listing_id' => $listingId,
            'subject' => $subject,
            'status' => 'active',
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(
        Conversation $conversation,
        int $senderId,
        string $content
    ): Message {
        $otherUserId = $conversation->buyer_id === $senderId
            ? $conversation->seller_id
            : $conversation->buyer_id;

        $message = $conversation->messages()->create([
            'sender_id' => $senderId,
            'content' => $content,
            'is_read' => false,
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        return $message;
    }

    /**
     * Mark all messages as read for a user.
     */
    public function markAllAsRead(int $userId, int $conversationId): void
    {
        Conversation::where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })
            ->where('id', $conversationId)
            ->first()?->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}
