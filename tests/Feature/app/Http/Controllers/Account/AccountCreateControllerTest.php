<?php

namespace Tests\Feature\app\Http\Controllers\Account;

use App\Models\Account;
use App\Models\Application;
use Tests\PmappTestCase;

class AccountCreateControllerTest extends PmappTestCase
{
    private Application $creatableApplication;

    private Application $nonCreatableApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creatableApplication = Application::factory()->create([
            'account_class' => true,
        ]);
        $this->nonCreatableApplication = Application::factory()->create([
            'account_class' => false,
        ]);
    }

    public function test_アカウントが正常に作成できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $payload = [
            'account' => [
                'name' => 'New Account',
                'application_id' => $this->creatableApplication->id,
                'notice_class' => true,
            ],
        ];

        $response = $this->postJson(route('accounts'), $payload);

        $response->assertOk();
        $this->assertDatabaseHas('accounts', $payload['account']);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->postJson(route('accounts'), [
            'account' => [
                'name' => 'New Account',
                'application_id' => $this->creatableApplication->id,
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(401);
    }

    public function test_必須項目未指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('accounts'), [
            'account' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'account.name',
            'account.application_id',
            'account.notice_class',
        ]);
    }

    public function test_型不正時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('accounts'), [
            'account' => [
                'name' => ['invalid'],
                'application_id' => 'invalid-id',
                'notice_class' => 'yes',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'account.name',
            'account.application_id',
            'account.notice_class',
        ]);
    }

    public function test_重複したアカウント名指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $existingAccount = Account::factory()->create([
            'application_id' => $this->creatableApplication->id,
            'name' => 'Duplicated Account',
        ]);

        $response = $this->postJson(route('accounts'), [
            'account' => [
                'name' => $existingAccount->name,
                'application_id' => $this->creatableApplication->id,
                'notice_class' => false,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['account.name']);
    }

    public function test_account_class_falseのアプリケーション指定時は403になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->postJson(route('accounts'), [
            'account' => [
                'name' => 'Forbidden Account',
                'application_id' => $this->nonCreatableApplication->id,
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('accounts', [
            'name' => 'Forbidden Account',
            'application_id' => $this->nonCreatableApplication->id,
        ]);
    }
}
