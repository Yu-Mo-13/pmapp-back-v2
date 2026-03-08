<?php

namespace Tests\Feature\app\Http\Controllers\Application;

use App\Models\Account;
use App\Models\Application;
use Tests\PmappTestCase;

class ApplicationAccountIndexControllerTest extends PmappTestCase
{
    private Application $targetApplication;

    private Application $otherApplication;

    private Account $targetAccount1;

    private Account $targetAccount2;

    private Account $otherAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetApplication = Application::factory()->create([
            'name' => 'Target App',
        ]);

        $this->otherApplication = Application::factory()->create([
            'name' => 'Other App',
        ]);

        $this->targetAccount1 = Account::factory()->create([
            'name' => 'Target Account 1',
            'application_id' => $this->targetApplication->id,
        ]);
        $this->targetAccount2 = Account::factory()->create([
            'name' => 'Target Account 2',
            'application_id' => $this->targetApplication->id,
        ]);
        $this->otherAccount = Account::factory()->create([
            'name' => 'Other Account',
            'application_id' => $this->otherApplication->id,
        ]);
    }

    public function test_正常取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('applications.accounts', ['application' => $this->targetApplication->id]));

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'id' => $this->targetAccount1->id,
            'name' => $this->targetAccount1->name,
        ]);
        $response->assertJsonFragment([
            'id' => $this->targetAccount2->id,
            'name' => $this->targetAccount2->name,
        ]);
        $response->assertJsonMissing([
            'id' => $this->otherAccount->id,
            'name' => $this->otherAccount->name,
        ]);
    }

    public function test_存在しないアプリケーションIDを指定した場合は404エラー(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('applications.accounts', ['application' => 999999]));

        $response->assertStatus(404);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('applications.accounts', ['application' => $this->targetApplication->id]));

        $response->assertStatus(401);
    }
}
