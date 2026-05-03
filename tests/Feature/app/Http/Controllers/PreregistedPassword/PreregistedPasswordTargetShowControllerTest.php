<?php

namespace Tests\Feature\app\Http\Controllers\PreregistedPassword;

use App\Models\Account;
use App\Models\Application;
use Tests\PmappTestCase;

class PreregistedPasswordTargetShowControllerTest extends PmappTestCase
{
    private Application $targetAccountClassTrueApplication;

    private Account $account;

    private Application $accountClassFalseApplication;

    private Account $otherApplicationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetAccountClassTrueApplication = Application::factory()->create([
            'account_class' => true,
        ]);
        $this->account = Account::factory()->create([
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'name' => '@test',
        ]);

        $this->accountClassFalseApplication = Application::factory()->create([
            'account_class' => false,
            'name' => 'アカウントなしアプリ',
        ]);

        $otherApplication = Application::factory()->create();
        $this->otherApplicationAccount = Account::factory()->create([
            'application_id' => $otherApplication->id,
        ]);
    }

    public function test_モバイル一般ユーザーがaccount_class_trueの対象情報を取得できること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertOk();
        $response->assertJson([
            'application' => [
                'id' => $this->targetAccountClassTrueApplication->id,
                'name' => $this->targetAccountClassTrueApplication->name,
            ],
            'account' => [
                'id' => $this->account->id,
                'name' => '@test',
            ],
        ]);
    }

    public function test_モバイル一般ユーザーがaccount_class_falseの対象情報を取得できること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->accountClassFalseApplication->id,
        ]));

        $response->assertOk();
        $response->assertJson([
            'application' => [
                'id' => $this->accountClassFalseApplication->id,
                'name' => 'アカウントなしアプリ',
            ],
            'account' => null,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertStatus(401);
    }

    public function test_管理者は利用できないこと(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_WEB一般ユーザーは利用できないこと(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => $this->account->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_application_id未指定時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target'));

        $response->assertStatus(404);
    }

    public function test_application_idが数値形式でない時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => 'invalid-id',
        ]));

        $response->assertStatus(404);
    }

    public function test_存在しないapplication_id指定時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => 999999,
        ]));

        $response->assertStatus(404);
    }

    public function test_account_class_falseでaccount_id指定時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $account = Account::factory()->create([
            'application_id' => $this->accountClassFalseApplication->id,
        ]);

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->accountClassFalseApplication->id,
            'account_id' => $account->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_account_class_trueでaccount_id未指定時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
        ]));

        $response->assertStatus(404);
    }

    public function test_account_class_trueでaccount_idが数値形式でない時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => 'invalid-id',
        ]));

        $response->assertStatus(404);
    }

    public function test_account_class_trueで存在しないaccount_id指定時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => 999999,
        ]));

        $response->assertStatus(404);
    }

    public function test_指定アプリケーションに紐づかないaccount_id指定時は404になること(): void
    {
        $this->actingAs($this->mobileUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.target', [
            'application_id' => $this->targetAccountClassTrueApplication->id,
            'account_id' => $this->otherApplicationAccount->id,
        ]));

        $response->assertStatus(404);
    }
}
