<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationUpdateRequest extends FormRequest
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
            'application.name' => 'required|string|max:255|unique:applications,name',
            'application.account_class' => 'required|boolean',
            'application.notice_class' => 'required|boolean',
            'application.mark_class' => 'required|boolean',
            'application.pre_password_size' => 'required|integer|min:1',
        ];
    }
}
