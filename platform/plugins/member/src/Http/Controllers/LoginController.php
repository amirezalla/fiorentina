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
    

    protected function attemptLogin(Request $request)
{
    $login = $request->email;

    // Retrieve the member using either email or username
    $member1 = \Botble\Member\Models\Member::where('email', $login)
                ->orWhere('user_login', $login)
                ->first();

    // If no member is found, immediately fail the login attempt
    if (!$member1) {
        return false;
    }

    // Set up WordPress password check
    $wp_hasher = new PasswordHash(8, false);
    $wpPassword = new WpPassword($wp_hasher);

    // If the password appears to be WordPress-hashed, use the custom checker
    if (strlen($member1->password) === 34 && substr($member1->password, 0, 3) === '$P$') {
        if ($wpPassword->check($request->password, $member1->password)) {
            if (setting('verify_account_email', config('plugins.member.general.verify_email')) && empty($member1->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        trans('plugins/member::member.not_confirmed', [
                            'resend_link' => route('public.member.resend_confirmation', ['email' => $member1->email]),
                        ]),
                    ],
                ]);
            }

            $this->guard()->login($member1, $request->filled('remember'));
            return true;
        }
    } else {
        // If not a WP hash, use the default validation and login attempt
        if ($this->guard()->validate($this->credentials($request))) {
            $member = $this->guard()->getLastAttempted();

            if (setting('verify_account_email', config('plugins.member.general.verify_email')) && empty($member->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        trans('plugins/member::member.not_confirmed', [
                            'resend_link' => route('public.member.resend_confirmation', ['email' => $member->email]),
                        ]),
                    ],
                ]);
            }

            return $this->baseAttemptLogin($request);
        }
    }
    if (!$wpPassword->check($request->password, $member1->password)) {
        return false;   // instead of falling through
    }

    return false;
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
