<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'first_name.required'    => 'Please enter your first name.',
            'first_name.max'         => 'First name may not be greater than 255 characters.',

            'last_name.required'    => 'Please enter your last name.',
            'last_name.max'         => 'Last name may not be greater than 255 characters.',

            'email.required'   => 'Please enter your email.',
            'email.email'      => 'Please enter a valid email address.',

            'country.required'    => 'Please enter your country.',
            'country.max'         => 'Country may not be greater than 255 characters.',

            'message.required' => 'Please enter your message.',
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'country' => 'required|string|max:255',
            'message' => 'required|string'
        ];
    }
}
