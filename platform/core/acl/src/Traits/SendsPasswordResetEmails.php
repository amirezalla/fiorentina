<?php

namespace Botble\ACL\Traits;

use Botble\Base\Rules\EmailRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

trait SendsPasswordResetEmails
{
    public function showLinkRequestForm()
    {
        return null;
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
    
        try {
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );
        } catch (\Exception $e) {
            dd('Error sending email: ' . $e->getMessage());
        }
    
        if ($response !== Password::RESET_LINK_SENT) {
            dd('Error sending reset link:', $response);
        }
    
        return $this->sendResetLinkResponse($request, $response);
    }

    protected function validateEmail(Request $request): void
    {
        $request->validate(['email' => ['required', new EmailRule()]]);
    }

    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($response)], 200)
            : back()->with('status', trans($response));
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    public function broker()
    {
        return Password::broker();
    }
}
