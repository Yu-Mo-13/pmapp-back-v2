<?php

namespace Tests\Feature\app\Http\Controllers\Password;

use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Illuminate\Support\Facades\Hash;
use Tests\PmappTestCase;

class PasswordCreateControllerTest extends PmappTestCase
{
    private Application $application;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->application = Application::factory()->create();
        $this->account = Account::factory()->create([
            'application_id' => $this->application->id,
        ]);
    }

    public function test_パスワードが正常に作成できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $payload = [
            'password' => [
                'password' => 'my-test-password',
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ];

        $response = $this->postJson(route('passwords.create'), $payload);
        $response->assertOk();

        $this->assertDatabaseHas('passwords', [
            'application_id' => $this->application->id,
            'account_id' => $this->account->id,
        ]);

        $createdPassword = Password::query()
            ->where('application_id', $this->application->id)
            ->where('account_id', $this->account->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($createdPassword);
        $this->assertNotEquals('my-test-password', $createdPassword->password);
        $this->assertTrue(Hash::check('my-test-password', $createdPassword->password));
    }

    public function test_未ログインの場合は401エラーになること(): void
    {
        $payload = [
            'password' => [
                'password' => 'my-test-password',
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ];

        $response = $this->postJson(route('passwords.create'), $payload);
        $response->assertStatus(401);
    }

    public function test_存在しないレコード指定時は422エラーになること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $payload = [
            'password' => [
                'password' => 'my-test-password',
                'application_id' => 999999,
                'account_id' => 999999,
            ],
        ];

        $response = $this->postJson(route('passwords.create'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password.application_id',
            'password.account_id',
        ]);
    }

    public function test_必須項目が未指定の場合は422エラーになること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('passwords.create'), [
            'password' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password.password',
            'password.application_id',
            'password.account_id',
        ]);
    }

    public function test_パスワードが256文字超の場合は422エラーになること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('passwords.create'), [
            'password' => [
                'password' => str_repeat('a', 256),
                'application_id' => $this->application->id,
                'account_id' => $this->account->id,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password.password']);
    }

    public function test_型不正の場合は422エラーになること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('passwords.create'), [
            'password' => [
                'password' => ['not-string'],
                'application_id' => 'invalid-id',
                'account_id' => 'invalid-id',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password.password',
            'password.application_id',
            'password.account_id',
        ]);
    }

    public function test_アプリケーションと紐付かないアカウント指定時は422エラーになること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $otherApplication = Application::factory()->create();
        $otherAccount = Account::factory()->create([
            'application_id' => $otherApplication->id,
        ]);

        $response = $this->postJson(route('passwords.create'), [
            'password' => [
                'password' => 'my-test-password',
                'application_id' => $this->application->id,
                'account_id' => $otherAccount->id,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password.account_id']);
    }
}
