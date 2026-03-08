<?php

namespace Tests\Feature\app\Http\Controllers\Account;

use App\Models\Application;
use Tests\PmappTestCase;

class AccountApplicationIndexControllerTest extends PmappTestCase
{
    private Application $accountClassTrueApplication1;

    private Application $accountClassTrueApplication2;

    private Application $accountClassFalseApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountClassTrueApplication1 = Application::factory()->create([
            'name' => 'Account Enabled App 1',
            'account_class' => true,
        ]);
        $this->accountClassTrueApplication2 = Application::factory()->create([
            'name' => 'Account Enabled App 2',
            'account_class' => true,
        ]);
        $this->accountClassFalseApplication = Application::factory()->create([
            'name' => 'Account Disabled App',
            'account_class' => false,
        ]);
    }

    public function test_正常取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('accounts.applications'));

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'id' => $this->accountClassTrueApplication1->id,
            'name' => $this->accountClassTrueApplication1->name,
        ]);
        $response->assertJsonFragment([
            'id' => $this->accountClassTrueApplication2->id,
            'name' => $this->accountClassTrueApplication2->name,
        ]);
        $response->assertJsonMissing([
            'id' => $this->accountClassFalseApplication->id,
            'name' => $this->accountClassFalseApplication->name,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('accounts.applications'));

        $response->assertStatus(401);
    }
}
