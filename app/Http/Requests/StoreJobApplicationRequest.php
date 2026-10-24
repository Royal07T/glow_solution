<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isJobSeeker() && $this->user()->hasVerifiedEmail();
    }

    public function rules(): array
    {
        return [
            'cv_id' => ['nullable', 'integer', 'exists:cvs,id'],
            'cover_letter' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->cv_id) {
                $cv = $this->user()->cvs()->find($this->cv_id);
                if (!$cv) {
                    $validator->errors()->add('cv_id', 'Invalid CV selected.');
                }
            }
        });
    }
}
