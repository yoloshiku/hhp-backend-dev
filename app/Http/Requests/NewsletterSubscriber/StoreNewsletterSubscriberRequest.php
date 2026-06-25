<?php

namespace App\Http\Requests\NewsletterSubscriber;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsletterSubscriberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom validation error messages.
     *
     * Defines user-friendly error messages for validation rules applied
     * to the request. These messages override Laravel's default validation
     * messages to provide clearer and more descriptive feedback to the client.
     *
     * @return array<string, string> An associative array where:
     * - The key represents the field and validation rule (e.g., 'email.required')
     * - The value is the custom error message returned when validation fails
     */
    public function messages(): array
    {
        return [
            'email.required'      => 'Email is required.',
            'email.email'         => 'Enter a valid email address.',
            'email.unique'        => 'This email is already registered.',
            'country.required'    => 'Country is required.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'country' => 'required|string|max:255',
        ];
    }
}
