<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
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
        $account = $this->route('account');

        return [
            'account.name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accounts', 'name')->ignore($account, 'id'),
            ],
            'account.application_id' => 'prohibited',
            'account.notice_class' => 'required|boolean',
        ];
    }
}
