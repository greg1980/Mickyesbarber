<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            abort(404);
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), $request->route('hash'))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('login', absolute: false).'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('login', absolute: false).'?verified=1');
    }
}
