<?php

namespace App\Http\Requests;

use App\Models\Job;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
        $job = $this->route('job');
        $jobId = $job instanceof Job ? $job->id : 0;

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
            'delete_workplace_photo_ids.*' => [
                'integer',
                Rule::exists('job_workplace_photos', 'id')->where('job_id', $jobId),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasSkills = ! empty($this->input('skills'));
            $hasNewSkills = ! empty(array_filter((array) $this->input('new_skills', [])));
            if (! $hasSkills && ! $hasNewSkills) {
                $validator->errors()->add('skills', 'Pilih minimal satu keahlian yang dibutuhkan atau tambahkan keahlian baru.');
            }

            if ($validator->errors()->hasAny(['workplace_photos', 'workplace_photos.*', 'delete_workplace_photo_ids', 'delete_workplace_photo_ids.*'])) {
                return;
            }

            $job = $this->route('job');
            if (! $job instanceof Job) {
                return;
            }

            $deletePhotoIds = collect((array) $this->input('delete_workplace_photo_ids', []))
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->all();
            $remainingPhotoCount = $job->workplacePhotos()
                ->when($deletePhotoIds !== [], fn (Builder $query): Builder => $query->whereNotIn('id', $deletePhotoIds))
                ->count();
            $newPhotoCount = count((array) $this->file('workplace_photos', []));

            if ($remainingPhotoCount + $newPhotoCount > 6) {
                $validator->errors()->add('workplace_photos', 'Total foto lingkungan kerja maksimal 6 foto, termasuk foto yang sudah tersimpan.');
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
