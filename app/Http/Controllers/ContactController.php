<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Services\WhatsAppLinkBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('frontend.contact', [
            'settings' => SiteSetting::cached(),
        ]);
    }

    public function store(StoreContactMessageRequest $request, WhatsAppLinkBuilder $whatsApp): RedirectResponse
    {
        $payload = $request->safe()->only(['name', 'email', 'phone', 'subject', 'message']);

        ContactMessage::query()->create([
            ...$payload,
            'ip_address' => $request->ip(),
        ]);

        $whatsappUrl = $whatsApp->build(
            SiteSetting::getValue('contact_whatsapp'),
            $whatsApp->messageFromContact($payload),
        );

        $redirect = back()->with('success', 'شكراً لتواصلكم. سنعود إليكم قريباً.');

        if ($whatsappUrl !== null) {
            $redirect->with('whatsapp_url', $whatsappUrl);
        }

        return $redirect;
    }
}
