<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        $devUsers = $this->devLoginEnabled()
            ? User::orderByDesc('role')->orderBy('name')->get()
            : collect();

        return view('auth.login', compact('devUsers'));
    }

    public function redirectToMicrosoft()
    {
        if (! config('services.microsoft.client_id') || ! config('services.microsoft.client_secret')) {
            return redirect()->route('login')->withErrors([
                'login' => 'Microsoft sign-in is not configured on this computer yet. Use a local demo account below, or configure the Microsoft Entra app credentials.',
            ]);
        }

        return Socialite::driver('microsoft')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }
    
    public function handleMicrosoftCallback(Request $request): RedirectResponse
    {
        try {
            $ms = Socialite::driver('microsoft')->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->withErrors(['login' => 'Microsoft sign-in failed. Please try again.']);
        }

        // The sign-in name (UPN) is verified by the user's own Microsoft tenant, so it can't be faked.
        $upn = strtolower(trim($ms->user['userPrincipalName'] ?? ''));
        $email = strtolower(trim($ms->getEmail() ?: $upn));

        if ($email === '') {
            return redirect()->route('login')
                ->withErrors(['login' => 'Your Microsoft account did not return an email address.']);
        }

        // Only school accounts (@mcc.edu.ph) may enter. Checks the UPN, falling back to the email.
        $domain = strtolower(trim((string) config('seeksave.allowed_domain')));
        if ($domain !== '') {
            $suffix = '@' . ltrim($domain, '@');
            $identity = $upn !== '' ? $upn : $email;

            if (! str_ends_with($identity, $suffix)) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Please sign in with your school Microsoft account (' . $suffix . ').']);
            }
        }

        $user = User::where('microsoft_id', $ms->getId())->first()
            ?? User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $ms->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'microsoft_id' => $ms->getId(),
            ]);
        } elseif (! $user->microsoft_id) {
            $user->update(['microsoft_id' => $ms->getId()]);
        }

        // Emails listed in ADMIN_EMAILS become admins (never auto-demoted).
        if (in_array($email, config('seeksave.admin_emails'), true) && ! $user->isAdmin()) {
            $user->forceFill(['role' => 'admin'])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('items.index'));
    }

    /** Local testing only (APP_ENV=local and SEEKSAVE_DEV_LOGIN=true). */
    public function devLogin(Request $request, User $user): RedirectResponse
    {
        abort_unless($this->devLoginEnabled(), 404);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('items.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function devLoginEnabled(): bool
    {
        return config('seeksave.dev_login') && app()->environment('local');
    }
}
