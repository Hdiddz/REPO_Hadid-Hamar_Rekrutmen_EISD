<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $job = $this->route('job');

        return $this->user()?->hasRole('admin')
            || ($this->user()?->hasRole('employer') && $job?->employer_id === $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:30', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'salary_type' => ['required', Rule::in(['hourly', 'daily', 'monthly'])],
            'salary_amount' => ['required', 'numeric', 'min:1', 'max:9999999999.99'],
            'work_hours_per_day' => ['required', 'integer', 'between:1,8'],
            'status' => ['required', Rule::in(['open', 'closed'])],
            'skills' => ['required', 'array', 'min:1'],
            'skills.*' => ['integer', 'distinct', 'exists:skills,id'],
        ];
    }

    public function messages(): array
    {
        return (new StoreJobRequest)->messages() + [
            'status.required' => 'Status lowongan wajib dipilih.',
            'status.in' => 'Status lowongan tidak valid.',
        ];
    }
}
