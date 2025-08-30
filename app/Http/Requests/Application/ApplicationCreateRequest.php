<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Enums\Application\ApplicationAccountClassEnum;
use App\Http\Enums\Application\ApplicationNoticeClassEnum;
use App\Http\Enums\Application\ApplicationMarkClassEnum;

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
            'account_class' => 'required|string|in:' . implode(',', array_values(ApplicationAccountClassEnum::getValues())),
            'notice_class' => 'required|string|in:' . implode(',', array_values(ApplicationNoticeClassEnum::getValues())),
            'mark_class' => 'required|string|in:' . implode(',', array_values(ApplicationMarkClassEnum::getValues())),
            'pre_password_size' => 'required|integer|min:1',
        ];
    }
}
