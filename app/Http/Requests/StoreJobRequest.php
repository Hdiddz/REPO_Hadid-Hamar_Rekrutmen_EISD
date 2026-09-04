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
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'workplace_photos' => ['nullable', 'array', 'max:6'],
            'workplace_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
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
            'cover_image.image' => 'Berkas cover lowongan harus berupa gambar yang valid.',
            'cover_image.mimes' => 'Format cover lowongan harus berupa JPG, JPEG, PNG, atau WEBP.',
            'cover_image.max' => 'Ukuran foto cover maksimal 5 MB.',
            'workplace_photos.max' => 'Foto lingkungan kerja maksimal 6 foto.',
            'workplace_photos.*.image' => 'Berkas foto lingkungan kerja harus berupa gambar.',
            'workplace_photos.*.mimes' => 'Format foto lingkungan kerja harus berupa JPG, JPEG, PNG, atau WEBP.',
            'workplace_photos.*.max' => 'Ukuran masing-masing foto lingkungan kerja maksimal 5 MB.',
        ];
    }
}
