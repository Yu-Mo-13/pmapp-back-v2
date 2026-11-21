<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UserIndexRequest extends FormRequest
{
    public const COUNTERSIGN_HEADER = 'X-Countersign';

    protected function prepareForValidation(): void
    {
        $expected = config('app.countersign');
        $provided = $this->header(self::COUNTERSIGN_HEADER);

        if (!is_string($expected) || $expected === '' || !is_string($provided) || !hash_equals($expected, $provided)) {
            throw ValidationException::withMessages(['countersign' => '合言葉を指定してください。']);
        }
    }

    public function rules(): array
    {
        return [];
    }
}
