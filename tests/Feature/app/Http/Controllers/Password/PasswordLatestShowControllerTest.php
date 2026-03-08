<?php

namespace Tests\Feature\app\Http\Controllers\Password;

use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Carbon\Carbon;
use Tests\PmappTestCase;

class PasswordLatestShowControllerTest extends PmappTestCase
{
    private Application $targetApplication;

    private Account $account;

    private Application $accountClassFalseApplication;

    private Account $otherApplicationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetApplication = Application::factory()->create([
            'account_class' => true,
        ]);
        $this->account = Account::factory()->create([
            'application_id' => $this->targetApplication->id,
        ]);

        $this->accountClassFalseApplication = Application::factory()->create([
            'account_class' => false,
        ]);

        $otherApplication = Application::factory()->create();
        $this->otherApplicationAccount = Account::factory()->create([
            'application_id' => $otherApplication->id,
        ]);
    }

    public function test_account_class_trueのときapplication_idとaccount_idで最新取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        Password::factory()->create([
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account->id,
            'password' => 'old-password',
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);
        $latestPassword = Password::factory()->create([
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account->id,
            'password' => 'latest-password',
            'created_at' => Carbon::parse('2026-02-01 00:00:00'),
        ]);

        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'password' => $latestPassword->password,
        ]);
    }

    public function test_account_class_falseのときapplication_idのみで最新取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $accountForFalseApplication = Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
        ]);

        Password::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => $accountForFalseApplication->id,
            'password' => 'old-password',
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);
        $latestPassword = Password::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => $accountForFalseApplication->id,
            'password' => 'latest-password',
            'created_at' => Carbon::parse('2026-02-01 00:00:00'),
        ]);

        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->accountClassFalseApplication->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'password' => $latestPassword->password,
        ]);
    }

    public function test_account_class_falseでaccount_id指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $accountForFalseApplication = Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
        ]);

        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => $accountForFalseApplication->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_account_class_trueでaccount_id未指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->targetApplication->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_指定アプリに紐づかないaccount_id指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->otherApplicationAccount->id,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['account_id']);
    }

    public function test_対象レコードが存在しない場合は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('passwords.latest', [
            'application_id' => $this->targetApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertStatus(401);
    }
}
