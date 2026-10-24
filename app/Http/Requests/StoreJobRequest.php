<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isEmployer();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'job_category_id' => ['nullable', 'exists:job_categories,id'],
            'type' => ['required', 'in:domestic,professional'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,temporary,live_in,live_out'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'salary_negotiable' => ['boolean'],
            'application_deadline' => ['nullable', 'date', 'after:today'],
            'positions_available' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
