<?php

namespace App\Modules\Messaging\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Models\MarketplaceListing;
use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class MessagingController extends Controller
{
    public function index(): InertiaResponse
    {
        $conversations = Conversation::query()
            ->forUser(auth()->id())
            ->with(['buyer', 'seller', 'listing', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return Inertia::render('Messaging/Index', [
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation): InertiaResponse
    {
        $this->authorize('view', $conversation);

        $conversation->load(['buyer', 'seller', 'listing', 'messages.sender']);

        $conversation->markAsRead(auth()->id());

        return Inertia::render('Messaging/Show', [
            'conversation' => $conversation,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => ['required', 'exists:marketplace_listings,id'],
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $listing = MarketplaceListing::findOrFail($validated['listing_id']);

        if ($listing->user_id === auth()->id()) {
            return back()->with('error', 'No puedes enviarte un mensaje a ti mismo');
        }

        $conversation = Conversation::firstOrCreate(
            [
                'buyer_id' => auth()->id(),
                'seller_id' => $listing->user_id,
                'listing_id' => $listing->id,
            ],
            [
                'subject' => $listing->title,
                'status' => 'active',
                'last_message_at' => now(),
            ]
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'content' => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('messaging.show', $conversation)
            ->with('success', 'Mensaje enviado correctamente');
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'content' => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Mensaje enviado');
    }

    public function markAsRead(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->markAsRead(auth()->id());

        return response()->json(['success' => true]);
    }

    public function archive(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->update(['status' => 'archived']);

        return redirect()->route('messaging.index')
            ->with('success', 'Conversación archivada');
    }
}
