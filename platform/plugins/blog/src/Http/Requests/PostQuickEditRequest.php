<?php

namespace Botble\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostQuickEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Update this logic as needed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|string|max:255',
            'slug'         => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'status'       => 'required|in:published,draft',
            // Add any additional rules for categories, tags, etc.
        ];
    }
}
