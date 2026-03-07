<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasswordLatestShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'application_id' => [
                'required',
                'integer',
                Rule::exists('applications', 'id')->whereNull('deleted_at'),
            ],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->where('application_id', $this->input('application_id'));
                    }),
            ],
        ];
    }
}
