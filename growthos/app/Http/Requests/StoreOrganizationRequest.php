<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'website'        => ['nullable', 'url', 'max:255'],
            'industry'       => ['nullable', 'string', 'max:100'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'country'        => ['nullable', 'string', 'max:100'],
            'timezone'       => ['nullable', 'string', 'max:100'],
            'logo'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'status'         => ['nullable', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required'    => 'Company name is required.',
            'website.url'      => 'Please enter a valid website URL (including https://).',
            'business_email.email' => 'Please enter a valid business email address.',
            'logo.image'       => 'The logo must be an image file.',
            'logo.mimes'       => 'The logo must be a JPEG, PNG, JPG, GIF, or WEBP file.',
            'logo.max'         => 'The logo may not be larger than 2MB.',
        ];
    }
}
