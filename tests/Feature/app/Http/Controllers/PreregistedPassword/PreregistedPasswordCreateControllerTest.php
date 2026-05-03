<?php

namespace Tests\Feature\app\Http\Controllers\PreregistedPassword;

use App\Models\Account;
use App\Models\Application;
use App\Models\PreregistedPassword;
use Illuminate\Support\Facades\Crypt;
use Tests\PmappTestCase;

class PreregistedPasswordCreateControllerTest extends PmappTestCase
{
    private const SYMBOLS = '!@#$%^&*';

    private Application $application;

    private Account $account;

    private Application $symbolApplication;

    private Account $symbolAccount;

    private Application $accountClassFalseApplication;

    private Account $accountClassFalseApplicationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->application = Application::factory()->create([
            'account_class' => true,
            'mark_class' => false,
            'pre_password_size' => 12,
        ]);
        $this->account = Account::factory()->create([
            'application_id' => $this->application->id,
        ]);

        $this->symbolApplication = Application::factory()->create([
            'account_class' => true,
            'mark_class' => true,
            'pre_password_size' => 10,
        ]);
        $this->symbolAccount = Account::factory()->create([
            'application_id' => $this->symbolApplication->id,
        ]);

        $this->accountClassFalseApplication = Application::factory()->create([
            'account_class' => false,
            'mark_class' => false,
            'pre_password_size' => 8,
        ]);
        $this->accountClassFalseApplicationAccount = Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
        ]);
    }

    public function test_管理者が記号なし仮登録パスワードを正常に作成できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ]);

        $response->assertOk();

        $createdPassword = PreregistedPassword::query()->latest('created_at')->first();

        $this->assertNotNull($createdPassword);
        $this->assertSame($this->application->id, $createdPassword->application_id);
        $this->assertSame($this->account->id, $createdPassword->account_id);
        $this->assertSame(12, strlen($createdPassword->password));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $createdPassword->password);

        $encryptedPassword = $createdPassword->getRawOriginal('password');
        $this->assertNotSame($createdPassword->password, $encryptedPassword);
        $this->assertSame($createdPassword->password, Crypt::decryptString($encryptedPassword));
    }

    public function test_管理者が記号あり仮登録パスワードを正常に作成できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->symbolApplication->id,
                'account_id' => $this->symbolAccount->id,
            ],
        ]);

        $response->assertOk();

        $createdPassword = PreregistedPassword::query()
            ->where('application_id', $this->symbolApplication->id)
            ->where('account_id', $this->symbolAccount->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($createdPassword);
        $this->assertSame(10, strlen($createdPassword->password));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9!@#\\$%\\^&\\*]+$/', $createdPassword->password);
        $this->assertTrue($this->containsSymbol($createdPassword->password));
    }

    public function test_WEB一般ユーザーが仮登録パスワードを正常に作成できること(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('preregisted_passwords', [
            'application_id' => $this->application->id,
            'account_id' => $this->account->id,
        ]);
    }

    public function test_モバイル一般ユーザーが仮登録パスワードを正常に作成できること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('preregisted_passwords', [
            'application_id' => $this->application->id,
            'account_id' => $this->account->id,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ]);

        $response->assertStatus(401);
    }

    public function test_存在しないレコード指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => 999999,
                'account_id' => 999999,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'preregisted_password.application_id',
            'preregisted_password.account_id',
        ]);
    }

    public function test_必須項目が未指定の場合は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'preregisted_password.application_id',
            'preregisted_password.account_id',
        ]);
    }

    public function test_型不正の場合は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => 'invalid-id',
                'account_id' => 'invalid-id',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'preregisted_password.application_id',
            'preregisted_password.account_id',
        ]);
    }

    public function test_アプリケーションと紐付かないアカウント指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $otherApplication = Application::factory()->create();
        $otherAccount = Account::factory()->create([
            'application_id' => $otherApplication->id,
        ]);

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->application->id,
                'account_id' => $otherAccount->id,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['preregisted_password.account_id']);
    }

    public function test_account_class_falseアプリではaccount_id未指定で作成できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->accountClassFalseApplication->id,
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('preregisted_passwords', [
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => null,
        ]);
    }

    public function test_account_class_falseアプリではaccount_id指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('preregisted-passwords.create'), [
            'preregisted_password' => [
                'application_id' => $this->accountClassFalseApplication->id,
                'account_id' => $this->accountClassFalseApplicationAccount->id,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['preregisted_password.account_id']);
    }

    private function containsSymbol(string $password): bool
    {
        return strpbrk($password, self::SYMBOLS) !== false;
    }
}
