<?php

namespace Tests\Feature\app\Http\Controllers\Account;

use App\Models\Account;
use App\Models\Application;
use Tests\PmappTestCase;

class AccountUpdateControllerTest extends PmappTestCase
{
    private Account $targetAccount;

    private Account $otherAccount;

    private Account $accountClassFalseApplicationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $targetApplication = Application::factory()->create([
            'account_class' => true,
        ]);
        $this->targetAccount = Account::factory()->create([
            'name' => 'Target Account',
            'application_id' => $targetApplication->id,
            'notice_class' => false,
        ]);
        $this->otherAccount = Account::factory()->create([
            'name' => 'Other Account',
            'application_id' => $targetApplication->id,
        ]);

        $accountClassFalseApplication = Application::factory()->create([
            'account_class' => false,
        ]);
        $this->accountClassFalseApplicationAccount = Account::factory()->create([
            'application_id' => $accountClassFalseApplication->id,
        ]);
    }

    public function test_アカウントが正常に更新できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('account', ['account' => $this->targetAccount->id]), [
            'account' => [
                'name' => 'Updated Account',
                'notice_class' => true,
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('accounts', [
            'id' => $this->targetAccount->id,
            'name' => 'Updated Account',
            'notice_class' => true,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->putJson(route('account', ['account' => $this->targetAccount->id]), [
            'account' => [
                'name' => 'Updated Account',
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(401);
    }

    public function test_必須項目未指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('account', ['account' => $this->targetAccount->id]), [
            'account' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'account.name',
            'account.notice_class',
        ]);
    }

    public function test_application_idを指定すると422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('account', ['account' => $this->targetAccount->id]), [
            'account' => [
                'name' => 'Updated Account',
                'application_id' => $this->targetAccount->application_id,
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['account.application_id']);
    }

    public function test_重複したアカウント名指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('account', ['account' => $this->targetAccount->id]), [
            'account' => [
                'name' => $this->otherAccount->name,
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['account.name']);
    }

    public function test_account_class_falseのアプリケーションに紐づく場合は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('account', ['account' => $this->accountClassFalseApplicationAccount->id]), [
            'account' => [
                'name' => 'Updated Account',
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(404);
    }

    public function test_存在しないアカウントID指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('account', ['account' => 999999]), [
            'account' => [
                'name' => 'Updated Account',
                'notice_class' => true,
            ],
        ]);

        $response->assertStatus(404);
    }
}
