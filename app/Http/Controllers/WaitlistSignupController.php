<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WaitlistSignupController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));
        $waitlistPath = storage_path('app/waitlist-emails.txt');

        if (! file_exists($waitlistPath)) {
            file_put_contents($waitlistPath, '');
        }

        $existing = file($waitlistPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        if (in_array($email, $existing, true)) {
            return redirect()->route('home')->with('success', 'Email already on the waitlist.');
        }

        file_put_contents($waitlistPath, $email.PHP_EOL, FILE_APPEND | LOCK_EX);

        return redirect()->route('home')->with('success', 'You are on the waitlist. We will update you before launch.');
    }
}
