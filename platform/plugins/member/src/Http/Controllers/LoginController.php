<?php

namespace Botble\Member\Http\Controllers;

use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Member\Forms\Fronts\Auth\LoginForm;
use Botble\Member\Http\Requests\Fronts\Auth\LoginRequest;
use Botble\Member\Models\Member;
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

    /* -------------------------------------------------------------------------- */
    /*  VIEW                                                                      */
    /* -------------------------------------------------------------------------- */

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

    /* -------------------------------------------------------------------------- */
    /*  LOGIN                                                                     */
    /* -------------------------------------------------------------------------- */

    public function login(LoginRequest $request)
    {
        $this->validateLogin($request);

        /* --- throttle -------------------------------------------------------- */
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        /* --- attempt --------------------------------------------------------- */
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        /* --- failed ---------------------------------------------------------- */
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);   // ★ pass $request
    }

    /**
     * Custom attemptLogin that understands both WordPress and Laravel hashes.
     *   – returns **true** on success, **false** on failure;
     *   – never returns a Response.
     */
    protected function attemptLogin(Request $request): bool
    {
        $login = $request->input('email');                       // field name in the form

        /** @var Member|null $member */
        $member = Member::query()
            ->where('email', $login)
            ->orWhere('user_login', $login)
            ->first();

        if (! $member) {
            return false;                                        // wrong username / e‑mail
        }

        /* -------- WordPress‑hash branch ------------------------------------ */
        $isWpHash = strlen($member->password) === 34
                 && str_starts_with($member->password, '$P$');

        if ($isWpHash) {
            $wpHasher   = new PasswordHash(8, false);
            $wpPassword = new WpPassword($wpHasher);

            if (! $wpPassword->check($request->password, $member->password)) {
                return false;                                    // wrong password
            }

        /* -------- Normal Laravel hash branch ------------------------------- */
        } else {
            if (! $this->guard()->validate($this->credentials($request))) {
                return false;                                    // wrong password
            }

            $member = $this->guard()->getLastAttempted();
        }

        /* -------- Account confirmation ------------------------------------- */
        if (
            setting('verify_account_email', config('plugins.member.general.verify_email')) &&
            empty($member->confirmed_at)
        ) {
            throw ValidationException::withMessages([
                'confirmation' => [
                    trans('plugins/member::member.not_confirmed', [
                        'resend_link' => route('public.member.resend_confirmation', ['email' => $member->email]),
                    ]),
                ],
            ]);
        }

        /* -------- Log the member in ---------------------------------------- */
        $this->guard()->login($member, $request->filled('remember'));

        return true;
    }

    /* -------------------------------------------------------------------------- */
    /*  GUARD & LOGOUT                                                            */
    /* -------------------------------------------------------------------------- */

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
