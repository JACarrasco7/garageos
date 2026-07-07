<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Contact/Edit', [
            'contact' => auth()->user()->contact,
        ]);
    }

    public function update(ContactUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->contact()->upsert($request->validated(), ['user_id']);

        return Redirect::route('contact.edit')->with('status', 'contact-updated');
    }
}
