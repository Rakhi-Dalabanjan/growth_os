<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->organization_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hexRegex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

        return [
            'brand_name'           => ['required', 'string', 'max:255'],
            'tagline'              => ['nullable', 'string', 'max:255'],
            'business_description' => ['nullable', 'string'],
            
            'mission'              => ['nullable', 'string'],
            'vision'               => ['nullable', 'string'],
            'primary_market'       => ['nullable', 'string', 'max:255'],
            'target_audience'      => ['nullable', 'string'],
            
            'brand_tone'           => ['nullable', 'string', 'max:255'],
            'formality'            => ['nullable', 'string', 'max:255'],
            'language'             => ['nullable', 'string', 'max:255'],
            'emoji_style'          => ['nullable', 'string', 'max:255'],
            
            'primary_color'        => ['nullable', 'string', 'regex:' . $hexRegex],
            'secondary_color'      => ['nullable', 'string', 'regex:' . $hexRegex],
            'accent_color'         => ['nullable', 'string', 'regex:' . $hexRegex],
            'primary_font'         => ['nullable', 'string', 'max:255'],
            'secondary_font'       => ['nullable', 'string', 'max:255'],
            
            'primary_cta'          => ['nullable', 'string', 'max:255'],
            'secondary_cta'        => ['nullable', 'string', 'max:255'],
            
            // These will be submitted as comma-separated strings from the form
            'preferred_words'      => ['nullable', 'string'],
            'restricted_words'     => ['nullable', 'string'],
            'competitor_names'     => ['nullable', 'string'],
            
            'approved_claims'      => ['nullable', 'string'],
            'restricted_claims'    => ['nullable', 'string'],
            'legal_disclaimer'     => ['nullable', 'string'],
            'status'               => ['nullable', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom error messages for validators.
     */
    public function messages(): array
    {
        return [
            'brand_name.required' => 'Brand name is required.',
            'primary_color.regex' => 'Primary color must be a valid hex code (e.g. #FF5733).',
            'secondary_color.regex' => 'Secondary color must be a valid hex code (e.g. #33FF57).',
            'accent_color.regex' => 'Accent color must be a valid hex code (e.g. #3357FF).',
        ];
    }
}
