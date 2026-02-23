<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasswordCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password.password' => 'required|string|max:255',
            'password.application_id' => [
                'required',
                'integer',
                Rule::exists('applications', 'id')->whereNull('deleted_at'),
            ],
            'password.account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->where('application_id', $this->input('password.application_id'));
                    }),
            ],
        ];
    }
}
