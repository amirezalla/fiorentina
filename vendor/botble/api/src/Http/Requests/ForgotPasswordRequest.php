<?php

namespace Botble\Api\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ForgotPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'string'],
        ];
    }

    public function bodyParameters()
    {
        return [
            'email' => [
                'example' => 'e.g: abc@example.com',
            ],
        ];
    }
}
