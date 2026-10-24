<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isJobSeeker();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }
}
