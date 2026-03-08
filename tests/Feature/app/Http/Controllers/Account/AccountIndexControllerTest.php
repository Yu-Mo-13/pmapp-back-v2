<?php

namespace Tests\Feature\app\Http\Controllers\Account;

use App\Models\Account;
use App\Models\Application;
use Tests\PmappTestCase;

class AccountIndexControllerTest extends PmappTestCase
{
    private Application $targetApplication;

    private Account $account1;

    private Account $account2;

    private Application $ignoredApplication;

    private Account $ignoredAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetApplication = Application::factory()->create([
            'name' => 'Target App',
            'account_class' => true,
        ]);

        $this->account1 = Account::factory()->create([
            'name' => 'Account 1',
            'application_id' => $this->targetApplication->id,
            'notice_class' => true,
        ]);
        $this->account2 = Account::factory()->create([
            'name' => 'Account 2',
            'application_id' => $this->targetApplication->id,
            'notice_class' => false,
        ]);

        $this->ignoredApplication = Application::factory()->create([
            'name' => 'Ignored App',
            'account_class' => false,
        ]);
        $this->ignoredAccount = Account::factory()->create([
            'name' => 'Ignored Account',
            'application_id' => $this->ignoredApplication->id,
        ]);
    }

    public function test_正常取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('accounts'));

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'id' => $this->account1->id,
            'name' => $this->account1->name,
            'application_id' => $this->targetApplication->id,
            'application_name' => $this->targetApplication->name,
            'notice_class' => (int) $this->account1->notice_class,
        ]);
        $response->assertJsonFragment([
            'id' => $this->account2->id,
            'name' => $this->account2->name,
            'application_id' => $this->targetApplication->id,
            'application_name' => $this->targetApplication->name,
            'notice_class' => (int) $this->account2->notice_class,
        ]);
        $response->assertJsonMissing([
            'id' => $this->ignoredAccount->id,
            'name' => $this->ignoredAccount->name,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('accounts'));

        $response->assertStatus(401);
    }
}
