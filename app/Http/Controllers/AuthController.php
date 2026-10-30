<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdhdAssessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(Request $request): View
    {
        return $this->authView($request, 'login');
    }

    public function register(Request $request): View
    {
        return $this->authView($request, 'register');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $this->setLocale($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => __('assessment.ui.auth_failed')])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', ['lang' => App::currentLocale()]));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->setLocale($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($validated);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard', ['lang' => App::currentLocale()]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard', ['lang' => App::currentLocale()]);
    }

    private function authView(Request $request, string $mode): View
    {
        $locale = $this->setLocale($request);

        return view('auth.account', [
            'mode' => $mode,
            'locale' => $locale,
            'isRtl' => $locale === 'fa',
            'locales' => $this->locales(),
            'ui' => $this->localizedArray('assessment.ui'),
        ]);
    }

    private function localizedArray(string $key): array
    {
        $fallback = trans($key, [], 'en');
        $current = trans($key);

        if (! is_array($fallback)) {
            return [];
        }

        if (! is_array($current)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $current);
    }

    private function setLocale(Request $request): string
    {
        $locale = $request->query('lang', $request->session()->get('locale', config('app.locale')));
        $locale = in_array($locale, AdhdAssessment::SUPPORTED_LOCALES, true) ? $locale : 'en';

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $locale;
    }

    private function locales(): array
    {
        return [
            'en' => 'English',
            'nl' => 'Nederlands',
            'fr' => 'Français',
            'fa' => 'فارسی',
        ];
    }
}
