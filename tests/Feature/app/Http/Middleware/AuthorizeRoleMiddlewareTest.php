<?php

namespace Tests\Feature\app\Http\Middleware;

use App\Models\Account;
use App\Models\Application;
use App\Models\PreregistedPassword;
use App\Models\UnregistedPassword;
use Tests\PmappTestCase;

class AuthorizeRoleMiddlewareTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $application = Application::factory()->create();
        $account = Account::factory()->create([
            'application_id' => $application->id,
        ]);

        PreregistedPassword::factory()->create([
            'application_id' => $application->id,
            'account_id' => $account->id,
        ]);
        UnregistedPassword::factory()->create([
            'application_id' => $application->id,
            'account_id' => $account->id,
        ]);
    }

    public function test_ApplicationAPIは全ロールアクセスできること(): void
    {
        $this->actingAs($this->webUser, 'api');
        $this->getJson(route('applications.index'))->assertOk();

        $this->actingAs($this->mobileUser, 'api');
        $this->getJson(route('applications.index'))->assertOk();
    }

    public function test_AccountAPIは管理者とWEB一般ユーザーのみアクセスできること(): void
    {
        $this->actingAs($this->adminUser, 'api');
        $this->getJson(route('accounts'))->assertOk();

        $this->actingAs($this->webUser, 'api');
        $this->getJson(route('accounts'))->assertOk();

        $this->actingAs($this->mobileUser, 'api');
        $this->getJson(route('accounts'))->assertNotFound();
    }

    public function test_PasswordAPIは全ロールアクセスできること(): void
    {
        $this->actingAs($this->adminUser, 'api');
        $this->getJson(route('passwords.index'))->assertOk();

        $this->actingAs($this->webUser, 'api');
        $this->getJson(route('passwords.index'))->assertOk();

        $this->actingAs($this->mobileUser, 'api');
        $this->getJson(route('passwords.index'))->assertOk();
    }

    public function test_UnregistedPasswordAPIは管理者と一般ユーザーのみアクセスできること(): void
    {
        $this->actingAs($this->adminUser, 'api');
        $this->getJson(route('unregisted-passwords.index'))->assertOk();

        $this->actingAs($this->webUser, 'api');
        $this->getJson(route('unregisted-passwords.index'))->assertOk();

        $this->actingAs($this->mobileUser, 'api');
        $this->getJson(route('unregisted-passwords.index'))->assertNotFound();
    }

    public function test_PreregistedPasswordAPIは全ロールアクセスできること(): void
    {
        $this->actingAs($this->adminUser, 'api');
        $this->getJson(route('preregisted-passwords.index'))->assertOk();

        $this->actingAs($this->webUser, 'api');
        $this->getJson(route('preregisted-passwords.index'))->assertOk();

        $this->actingAs($this->mobileUser, 'api');
        $this->getJson(route('preregisted-passwords.index'))->assertOk();
    }
}
