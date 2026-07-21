<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactRequest;
use App\Mail\ContactMessageMail;
use App\Services\SiteSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function submit(ContactRequest $request, SiteSettingService $settings): RedirectResponse
    {
        $recipient = $settings->get('site_email', config('mail.from.address'));

        Mail::to($recipient)->send(new ContactMessageMail(
            $request->name,
            $request->email,
            $request->subject,
            $request->message,
        ));

        return back()->with('success', 'Thanks — we\'ll get back to you soon!');
    }
}
