<?php

namespace Tests\Feature\app\Http\Controllers\Password;

use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Carbon\Carbon;
use Tests\PmappTestCase;

class PasswordUpdatePromoteIndexControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-03-15 00:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_未ログインでも更新促進対象を取得できること(): void
    {
        $applicationWithoutAccount = Application::factory()->create([
            'id' => 2,
            'name' => 'アカウントなしアプリケーション',
            'account_class' => false,
            'notice_class' => true,
        ]);
        Password::factory()->create([
            'application_id' => $applicationWithoutAccount->id,
            'account_id' => null,
            'created_at' => Carbon::parse('2025-09-15 00:00:00'),
        ]);

        $applicationWithAccount = Application::factory()->create([
            'id' => 1,
            'name' => 'アカウントありアプリケーション',
            'account_class' => true,
            'notice_class' => true,
        ]);
        $targetAccount = Account::factory()->create([
            'id' => 3,
            'name' => '@target',
            'application_id' => $applicationWithAccount->id,
            'notice_class' => true,
        ]);
        Password::factory()->create([
            'application_id' => $applicationWithAccount->id,
            'account_id' => $targetAccount->id,
            'created_at' => Carbon::parse('2025-09-14 23:59:59'),
        ]);

        $excludedAccount = Account::factory()->create([
            'id' => 4,
            'name' => '@excluded',
            'application_id' => $applicationWithAccount->id,
            'notice_class' => true,
        ]);
        Password::factory()->create([
            'application_id' => $applicationWithAccount->id,
            'account_id' => $excludedAccount->id,
            'created_at' => Carbon::parse('2025-09-16 00:00:00'),
        ]);

        Application::factory()->create([
            'id' => 5,
            'name' => 'パスワード未登録アプリケーション',
            'account_class' => false,
            'notice_class' => true,
        ]);

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([
                [
                    'application' => [
                        'id' => $applicationWithAccount->id,
                        'name' => $applicationWithAccount->name,
                    ],
                    'account' => [
                        'id' => $targetAccount->id,
                        'name' => $targetAccount->name,
                    ],
                ],
                [
                    'application' => [
                        'id' => $applicationWithoutAccount->id,
                        'name' => $applicationWithoutAccount->name,
                    ],
                    'account' => null,
                ],
            ]);
    }

    public function test_account_class_falseはaccount_id_nullの最新パスワードのみで判定すること(): void
    {
        $application = Application::factory()->create([
            'account_class' => false,
            'notice_class' => true,
        ]);

        Password::factory()->create([
            'application_id' => $application->id,
            'account_id' => null,
            'created_at' => Carbon::parse('2025-09-10 00:00:00'),
        ]);
        $account = Account::factory()->create([
            'application_id' => $application->id,
            'notice_class' => true,
        ]);
        Password::factory()->create([
            'application_id' => $application->id,
            'account_id' => $account->id,
            'created_at' => Carbon::parse('2026-03-01 00:00:00'),
        ]);

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([
                [
                    'application' => [
                        'id' => $application->id,
                        'name' => $application->name,
                    ],
                    'account' => null,
                ],
            ]);
    }

    public function test_閾値ちょうどの更新日は通知対象に含まれること(): void
    {
        $application = Application::factory()->create([
            'name' => '境界日対象アプリ',
            'account_class' => false,
            'notice_class' => true,
        ]);

        Password::factory()->create([
            'application_id' => $application->id,
            'account_id' => null,
            'created_at' => Carbon::parse('2025-09-15 00:00:00'),
        ]);

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([
                [
                    'application' => [
                        'id' => $application->id,
                        'name' => $application->name,
                    ],
                    'account' => null,
                ],
            ]);
    }

    public function test_閾値を1秒でも超える更新日は通知対象に含まれないこと(): void
    {
        $application = Application::factory()->create([
            'name' => '境界日除外アプリ',
            'account_class' => false,
            'notice_class' => true,
        ]);

        Password::factory()->create([
            'application_id' => $application->id,
            'account_id' => null,
            'created_at' => Carbon::parse('2025-09-15 00:00:01'),
        ]);

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([]);
    }

    public function test_notice_class_falseのアプリケーションは通知対象に含まれないこと(): void
    {
        $application = Application::factory()->create([
            'name' => '通知対象外アプリ',
            'account_class' => false,
            'notice_class' => false,
        ]);

        Password::factory()->create([
            'application_id' => $application->id,
            'account_id' => null,
            'created_at' => Carbon::parse('2025-09-01 00:00:00'),
        ]);

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([]);
    }

    public function test_notice_class_falseのアカウントは通知対象に含まれないこと(): void
    {
        $application = Application::factory()->create([
            'name' => '通知対象アプリ',
            'account_class' => true,
            'notice_class' => true,
        ]);

        $excludedAccount = Account::factory()->create([
            'name' => '@excluded',
            'application_id' => $application->id,
            'notice_class' => false,
        ]);
        Password::factory()->create([
            'application_id' => $application->id,
            'account_id' => $excludedAccount->id,
            'created_at' => Carbon::parse('2025-09-01 00:00:00'),
        ]);

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([]);
    }

    public function test_削除済みアプリケーションとそのアカウントは通知対象に含まれないこと(): void
    {
        $applicationWithoutAccount = Application::factory()->create([
            'name' => '削除済みアカウントなしアプリ',
            'account_class' => false,
            'notice_class' => true,
        ]);
        Password::factory()->create([
            'application_id' => $applicationWithoutAccount->id,
            'account_id' => null,
            'created_at' => Carbon::parse('2025-09-01 00:00:00'),
        ]);
        $applicationWithoutAccount->delete();

        $applicationWithAccount = Application::factory()->create([
            'name' => '削除済みアカウントありアプリ',
            'account_class' => true,
            'notice_class' => true,
        ]);
        $account = Account::factory()->create([
            'name' => '@deleted-app-account',
            'application_id' => $applicationWithAccount->id,
            'notice_class' => true,
        ]);
        Password::factory()->create([
            'application_id' => $applicationWithAccount->id,
            'account_id' => $account->id,
            'created_at' => Carbon::parse('2025-09-01 00:00:00'),
        ]);
        $applicationWithAccount->delete();

        $response = $this->getJson(route('password-update-promote.index'));

        $response->assertOk()
            ->assertExactJson([]);
    }
}
