<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestbookEntryRequest;
use App\Models\GuestbookEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuestbookController extends Controller
{
    public function index(): View
    {
        return view('guestbook', [
            'entries' => GuestbookEntry::query()->recent()->get(),
        ]);
    }

    public function store(StoreGuestbookEntryRequest $request): RedirectResponse
    {
        GuestbookEntry::create($request->validated());

        return redirect()
            ->route('guestbook.index')
            ->with('status', 'Thanks for signing the guestbook!');
    }
}
