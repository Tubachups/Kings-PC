<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsRequest;
use App\Mail\ContactUs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    public function sendEmail(ContactUsRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        Mail::to($request->user())
            ->queue(new ContactUs(
                name: $validatedData['name'],
                email: $validatedData['email'],
                messageContent: $validatedData['message'],
            ));

        return redirect()
            ->route('contacts')
            ->with('success', 'Your message has been sent successfully.');
    }
}
