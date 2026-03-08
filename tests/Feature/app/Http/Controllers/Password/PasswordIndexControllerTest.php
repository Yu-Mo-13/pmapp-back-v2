<?php

namespace Tests\Feature\app\Http\Controllers\Password;

use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Illuminate\Support\Carbon;
use Tests\PmappTestCase;

class PasswordIndexControllerTest extends PmappTestCase
{
    private Application $targetApplication;

    private Account $account1;

    private Account $account2;

    private Application $accountClassFalseApplication;

    private Application $deletedApplication;

    private Carbon $account1LatestUpdatedAt;

    private Carbon $account2LatestUpdatedAt;

    private Carbon $accountClassFalseLatestUpdatedAt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetApplication = Application::factory()->create([
            'account_class' => true,
        ]);
        $this->account1 = Account::factory()->create([
            'application_id' => $this->targetApplication->id,
            'name' => 'Account 1',
        ]);
        $this->account2 = Account::factory()->create([
            'application_id' => $this->targetApplication->id,
            'name' => 'Account 2',
        ]);

        $this->accountClassFalseApplication = Application::factory()->create([
            'account_class' => false,
        ]);
        Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
            'name' => 'Ignored Account',
        ]);

        $this->deletedApplication = Application::factory()->create([
            'account_class' => false,
        ]);
        $this->deletedApplication->delete();

        $this->account1LatestUpdatedAt = Carbon::parse('2026-03-01 10:00:00');
        Password::factory()->create([
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account1->id,
            'updated_at' => Carbon::parse('2026-02-01 10:00:00'),
        ]);
        Password::factory()->create([
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account1->id,
            'updated_at' => $this->account1LatestUpdatedAt,
        ]);

        $this->account2LatestUpdatedAt = Carbon::parse('2026-01-15 09:00:00');
        Password::factory()->create([
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account2->id,
            'updated_at' => $this->account2LatestUpdatedAt,
        ]);

        $this->accountClassFalseLatestUpdatedAt = Carbon::parse('2026-04-01 12:00:00');
        $accountClassFalseAccount1 = Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
        ]);
        $accountClassFalseAccount2 = Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
        ]);

        Password::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => $accountClassFalseAccount1->id,
            'updated_at' => Carbon::parse('2026-02-10 12:00:00'),
        ]);
        Password::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => $accountClassFalseAccount2->id,
            'updated_at' => $this->accountClassFalseLatestUpdatedAt,
        ]);
    }

    public function test_正常取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('passwords.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJsonFragment([
            'latest_updated_at' => $this->account1LatestUpdatedAt->toISOString(),
            'application' => [
                'id' => $this->targetApplication->id,
                'name' => $this->targetApplication->name,
            ],
            'account' => [
                'id' => $this->account1->id,
                'name' => $this->account1->name,
            ],
        ]);
        $response->assertJsonFragment([
            'latest_updated_at' => $this->account2LatestUpdatedAt->toISOString(),
            'application' => [
                'id' => $this->targetApplication->id,
                'name' => $this->targetApplication->name,
            ],
            'account' => [
                'id' => $this->account2->id,
                'name' => $this->account2->name,
            ],
        ]);
        $response->assertJsonFragment([
            'latest_updated_at' => $this->accountClassFalseLatestUpdatedAt->toISOString(),
            'application' => [
                'id' => $this->accountClassFalseApplication->id,
                'name' => $this->accountClassFalseApplication->name,
            ],
            'account' => null,
        ]);

        $response->assertJsonMissing([
            'application' => [
                'id' => $this->deletedApplication->id,
                'name' => $this->deletedApplication->name,
            ],
        ]);
    }

    public function test_application_idで絞り込みできること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('passwords.index', [
            'application_id' => $this->targetApplication->id,
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'application' => [
                'id' => $this->targetApplication->id,
                'name' => $this->targetApplication->name,
            ],
        ]);
        $response->assertJsonMissing([
            'application' => [
                'id' => $this->accountClassFalseApplication->id,
                'name' => $this->accountClassFalseApplication->name,
            ],
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('passwords.index'));
        $response->assertStatus(401);
    }

    public function test_存在しないapplication_id指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('passwords.index', [
            'application_id' => 999999,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application_id']);
    }
}
