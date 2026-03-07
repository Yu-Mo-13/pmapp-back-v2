<?php

namespace Tests\Feature\app\Http\Controllers\Account;

use App\Models\Account;
use App\Models\Application;
use Tests\PmappTestCase;

class AccountShowControllerTest extends PmappTestCase
{
    private Account $targetAccount;

    private Account $accountClassFalseApplicationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $targetApplication = Application::factory()->create([
            'account_class' => true,
        ]);
        $this->targetAccount = Account::factory()->create([
            'application_id' => $targetApplication->id,
        ]);

        $accountClassFalseApplication = Application::factory()->create([
            'account_class' => false,
        ]);
        $this->accountClassFalseApplicationAccount = Account::factory()->create([
            'application_id' => $accountClassFalseApplication->id,
        ]);
    }

    public function test_正常取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('account', ['account' => $this->targetAccount->id]));

        $response->assertOk();
        $response->assertJson([
            'id' => $this->targetAccount->id,
            'name' => $this->targetAccount->name,
            'application_id' => $this->targetAccount->application_id,
            'notice_class' => $this->targetAccount->notice_class,
        ]);
        $responseData = $response->json();
        $this->assertArrayNotHasKey('created_at', $responseData);
        $this->assertArrayNotHasKey('updated_at', $responseData);
    }

    public function test_account_class_falseのアプリケーションに紐づく場合は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('account', ['account' => $this->accountClassFalseApplicationAccount->id]));

        $response->assertStatus(404);
    }

    public function test_存在しないアカウントID指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('account', ['account' => 999999]));

        $response->assertStatus(404);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('account', ['account' => $this->targetAccount->id]));

        $response->assertStatus(401);
    }
}
