<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationCreateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'account_class' => 'required|integer',
            'notice_class' => 'required|integer',
            'mark_class' => 'required|integer',
            'pre_password_size' => 'required|integer|min:8|max:64',
        ];
    }
}
