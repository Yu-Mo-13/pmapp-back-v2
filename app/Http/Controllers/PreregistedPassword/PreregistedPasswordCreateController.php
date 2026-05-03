<?php

namespace App\Http\Controllers\PreregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreregistedPassword\PreregistedPasswordCreateRequest;
use App\Models\Application;
use App\Models\PreregistedPassword;
use Illuminate\Http\JsonResponse;

class PreregistedPasswordCreateController extends Controller
{
    private const ALPHABET_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const ALPHABET_LOWER = 'abcdefghijklmnopqrstuvwxyz';

    private const NUMBERS = '0123456789';

    private const SYMBOLS = '!@#$%^&*';

    public function __invoke(PreregistedPasswordCreateRequest $request): JsonResponse
    {
        $preregistedPasswordData = $request->input('preregisted_password');
        $application = Application::query()->findOrFail($preregistedPasswordData['application_id']);

        PreregistedPassword::create([
            'password' => $this->generatePassword($application->pre_password_size, (bool) $application->mark_class),
            'application_id' => $preregistedPasswordData['application_id'],
            'account_id' => $preregistedPasswordData['account_id'],
        ]);

        return ApiResponseFormatter::ok();
    }

    private function generatePassword(int $length, bool $includeSymbols): string
    {
        $alphaNumericCharacters = self::ALPHABET_UPPER . self::ALPHABET_LOWER . self::NUMBERS;

        if (! $includeSymbols) {
            return $this->randomCharactersFrom($alphaNumericCharacters, $length);
        }

        $characters = $this->randomCharactersFrom(
            $alphaNumericCharacters . self::SYMBOLS,
            max(0, $length - 1)
        ) . $this->randomCharactersFrom(self::SYMBOLS, 1);

        return str_shuffle($characters);
    }

    private function randomCharactersFrom(string $characters, int $length): string
    {
        $generated = '';
        $maxIndex = strlen($characters) - 1;

        for ($index = 0; $index < $length; $index++) {
            $generated .= $characters[random_int(0, $maxIndex)];
        }

        return $generated;
    }
}
