<?php

namespace App\Http\Requests\PreregistedPassword;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->where('application_id', $this->input('preregisted_password.application_id'));
                    }),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $preregistedPassword = $this->input('preregisted_password', []);
            $accountIdExists = is_array($preregistedPassword) && array_key_exists('account_id', $preregistedPassword);
            $accountId = $accountIdExists ? $preregistedPassword['account_id'] : null;
            $application = Application::query()->find(data_get($preregistedPassword, 'application_id'));
            $accountIdField = 'preregisted_password.account_id';

            $requiresAccountId = (! $application || $application->account_class) && ! $accountIdExists;
            $prohibitsAccountId = $application
                && ! $application->account_class
                && $accountIdExists
                && ! is_null($accountId);

            if ($requiresAccountId) {
                $validator->errors()->add(
                    $accountIdField,
                    'The preregisted_password.account_id field is required.'
                );
            }

            if ($prohibitsAccountId) {
                $validator->errors()->add(
                    $accountIdField,
                    'The preregisted_password.account_id field is prohibited.'
                );
            }
        });
    }
}
