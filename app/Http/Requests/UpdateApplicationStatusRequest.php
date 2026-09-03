<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $application = $this->route('application');

        return $this->user()?->hasRole('admin')
            || ($this->user()?->hasRole('employer') && $application?->job()->where('employer_id', $this->user()->id)->exists());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'interview', 'accepted', 'rejected'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status lamaran wajib dipilih.',
            'status.in' => 'Status lamaran tidak valid.',
        ];
    }
}
