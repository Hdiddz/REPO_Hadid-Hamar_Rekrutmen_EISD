<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('jobseeker') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resume' => ['required', 'file', 'mimes:pdf', 'extensions:pdf', 'max:2048'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'resume.required' => 'Resume PDF wajib diunggah.',
            'resume.mimes' => 'Resume harus berupa berkas PDF.',
            'resume.extensions' => 'Ekstensi resume harus .pdf.',
            'resume.max' => 'Ukuran resume maksimal 2 MB.',
            'note.max' => 'Catatan pengalaman maksimal 2.000 karakter.',
        ];
    }
}
