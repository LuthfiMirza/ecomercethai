<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Rate limiting: 3 messages per hour per IP
        $key = 'contact-form:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', __('contact.rate_limit', ['minutes' => ceil($seconds / 60)]));
        }

        RateLimiter::hit($key, 3600); // 1 hour

        try {
            Mail::to(config('mail.from.address'))->send(new ContactFormMail([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]));

            return back()->with('success', __('contact.success'));
        } catch (\Exception $e) {
            return back()->with('error', __('contact.error'));
        }
    }
}
