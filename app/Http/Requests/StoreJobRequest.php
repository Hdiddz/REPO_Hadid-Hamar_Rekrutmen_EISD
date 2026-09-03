<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('employer') ?? false;
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
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'distinct', 'exists:skills,id'],
            'new_skills' => ['nullable', 'array'],
            'new_skills.*' => ['string', 'max:100'],
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
        return [
            'category_id.required' => 'Kategori pekerjaan wajib dipilih.',
            'title.required' => 'Judul lowongan wajib diisi.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'description.min' => 'Deskripsi pekerjaan minimal 30 karakter.',
            'location.required' => 'Lokasi kerja wajib diisi.',
            'salary_type.required' => 'Skema pembayaran wajib dipilih.',
            'salary_amount.required' => 'Nominal upah wajib diisi secara transparan.',
            'work_hours_per_day.between' => 'Jam kerja harus berada di antara 1 sampai 8 jam per hari.',
            'skills.required' => 'Pilih minimal satu keahlian yang dibutuhkan.',
            'skills.min' => 'Pilih minimal satu keahlian yang dibutuhkan.',
        ];
    }
}
