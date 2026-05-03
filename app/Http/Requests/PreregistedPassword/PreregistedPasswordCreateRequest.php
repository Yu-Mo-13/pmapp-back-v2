<?php

namespace App\Http\Requests\PreregistedPassword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreregistedPasswordCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'preregisted_password.application_id' => [
                'required',
                'integer',
                Rule::exists('applications', 'id')->whereNull('deleted_at'),
            ],
            'preregisted_password.account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->where('application_id', $this->input('preregisted_password.application_id'));
                    }),
            ],
        ];
    }
}
