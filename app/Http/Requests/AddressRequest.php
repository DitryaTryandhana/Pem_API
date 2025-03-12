<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['nullable', 'max:255'],
            'city' => ['nullable', 'max:100'],
            'province' => ['nullable', 'max:100'],
            'country' => ['required', 'max:100'],
            'postal_code' => ['nullable', 'max:10']
        ];
    }
}
