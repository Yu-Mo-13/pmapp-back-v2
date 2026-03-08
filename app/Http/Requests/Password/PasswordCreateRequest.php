<?php

namespace App\Http\Requests\Password;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->where('application_id', $this->input('password.application_id'));
                    }),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $password = $this->input('password', []);
            $accountIdExists = is_array($password) && array_key_exists('account_id', $password);
            $accountId = $accountIdExists ? $password['account_id'] : null;
            $application = Application::query()->find(data_get($password, 'application_id'));

            if ((!$application || $application->account_class) && !$accountIdExists) {
                $validator->errors()->add('password.account_id', 'The password.account_id field is required.');
            }

            if ($application && !$application->account_class && $accountIdExists && !is_null($accountId)) {
                $validator->errors()->add('password.account_id', 'The password.account_id field is prohibited.');
            }
        });
    }

}
