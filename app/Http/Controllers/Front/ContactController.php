<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('Contact form submission started');

        try {
            $request->validate([
                'email' => 'required|email',
                'name' => 'required|string',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);

            \Log::info('Validation passed', $request->all());

            Mail::to('storytail.isla@gmail.com')->send(new ContactFormMail($request->all()));
            \Log::info('Email sent successfully');

            return redirect()->back()->with('success', 'Thank you for your message. We will contact you soon!');
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Sorry, there was an error sending your message.')
                ->withInput();
        }
    }
}
