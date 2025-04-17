<?php

namespace Botble\Member\Http\Controllers;

use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Member\Forms\Fronts\Auth\LoginForm;
use Botble\Member\Http\Requests\Fronts\Auth\LoginRequest;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Hautelook\Phpass\PasswordHash;
use MikeMcLin\WpPassword\WpPassword;


class LoginController extends BaseController
{
    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
    }

    public function showLoginForm()
    {
        SeoHelper::setTitle(trans('plugins/member::member.login'));

        if (! session()->has('url.intended')) {
            session(['url.intended' => url()->previous()]);
        }

        Theme::breadcrumb()->add(__('Login'), route('public.member.login'));

        return Theme::scope(
            'member.auth.login',
            ['form' => LoginForm::create()],
            'plugins/member::themes.auth.login'
        )->render();
    }

    public function login(LoginRequest $request)
    {
        $this->validateLogin($request);
    
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);   // ← return!
        }
    
        try {
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request); // ← return!
            }
        } catch (\Throwable $e) {
            // If the custom attemptLogin threw a ValidationException (un‑confirmed account)
            // just let Laravel handle it, but still count a failed attempt if you wish.
            $this->incrementLoginAttempts($request);
            throw $e;
        }
    
        // login failed – increment counter then throw validation error
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);   // ← always return
    }
    

    protected function attemptLogin(Request $request): bool
    {
        $login  = $request->email;                               // can be e‑mail or username
        $member = \Botble\Member\Models\Member::where('email', $login)
                  ->orWhere('user_login', $login)
                  ->first();
    
        // No user → immediate failure (counted by caller)
        if (!$member) {
            return false;
        }
    
        // ------------------------------------------------------------------
        // 1)  WordPress‑hashed password?
        // ------------------------------------------------------------------
        $isWpHash = strlen($member->password) === 34 && str_starts_with($member->password, '$P$');
    
        if ($isWpHash) {
            $wpPassword = new \WpPassword(new \PasswordHash(8, false));
    
            if (! $wpPassword->check($request->password, $member->password)) {
                return false;                           // wrong password
            }
    
            // confirmed?
            if ($this->needsConfirmation($member)) {
                $this->throwNotConfirmed($member);
            }
    
            $this->guard()->login($member, $request->filled('remember'));
            return true;                                // success
        }
    
        // ------------------------------------------------------------------
        // 2)  Normal bcrypt hash (Laravel default)
        // ------------------------------------------------------------------
        if (! $this->guard()->validate($this->credentials($request))) {
            return false;                               // wrong credentials
        }
    
        $lastAttempted = $this->guard()->getLastAttempted();
    
        if ($this->needsConfirmation($lastAttempted)) {
            $this->throwNotConfirmed($lastAttempted);
        }
    
        // baseAttemptLogin does guard()->login() and remember‑me handling
        return $this->baseAttemptLogin($request);
    }
    
    /* ---------- small private helpers ---------------------------------- */
    
    private function needsConfirmation($member): bool
    {
        return setting('verify_account_email',
                       config('plugins.member.general.verify_email'))
               && empty($member->confirmed_at);
    }
    
    private function throwNotConfirmed($member): void
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'confirmation' => [
                trans('plugins/member::member.not_confirmed', [
                    'resend_link' => route('public.member.resend_confirmation',
                                           ['email' => $member->email]),
                ]),
            ],
        ]);
    }

    protected function guard()
    {
        return auth('member');
    }

    public function logout(Request $request)
    {
        $activeGuards = 0;
        $this->guard()->logout();

        foreach (config('auth.guards', []) as $guard => $guardConfig) {
            if ($guardConfig['driver'] !== 'session') {
                continue;
            }
            if ($this->isActiveGuard($request, $guard)) {
                $activeGuards++;
            }
        }

        if (! $activeGuards) {
            $request->session()->flush();
            $request->session()->regenerate();
        }

        $this->loggedOut($request);

        return redirect()->to(BaseHelper::getHomepageUrl());
    }
}
