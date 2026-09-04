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
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'distinct', 'exists:skills,id'],
            'new_skills' => ['nullable', 'array'],
            'new_skills.*' => ['string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'workplace_photos' => ['nullable', 'array', 'max:6'],
            'workplace_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'delete_workplace_photo_ids' => ['nullable', 'array'],
            'delete_workplace_photo_ids.*' => ['integer', 'exists:job_workplace_photos,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v): void {
            $hasSkills = ! empty($this->input('skills'));
            $hasNewSkills = ! empty(array_filter((array) $this->input('new_skills', [])));
            if (! $hasSkills && ! $hasNewSkills) {
                $v->errors()->add('skills', 'Pilih minimal satu keahlian yang dibutuhkan atau tambahkan keahlian baru.');
            }
        });
    }

    public function messages(): array
    {
        return (new StoreJobRequest)->messages() + [
            'status.required' => 'Status lowongan wajib dipilih.',
            'status.in' => 'Status lowongan tidak valid.',
        ];
    }
}
