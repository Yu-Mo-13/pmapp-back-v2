<?php

namespace Tests;

use App\Http\Enums\Role\RoleEnum;
use App\Models\Application;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmappTestCase extends TestCase
{
    use RefreshDatabase;
    /**
     * @var Application
     */
    protected Application $markClassTrueApplication;
    /**
     * @var Application
     */
    protected Application $accountClassTrueApplication;
    /**
     * @var Application
     */
    protected Application $noticeClassTrueApplication;
    /**
     * @var Role
     */
    protected Role $adminRole;
    /**
     * @var Role
     */
    protected Role $webUserRole;
    /**
     * @var Role
     */
    protected Role $mobileUserRole;
    /**
     * @var User
     */
    protected User $adminUser;
    /**
     * @var User
     */
    protected User $webUser;
    /**
     * @var User
     */
    protected User $mobileUser;

    protected function setUp(): void
    {
        parent::setUp();

        // ロールの作成
        $this->adminRole = Role::factory()->create([
            'name' => RoleEnum::getDescription(RoleEnum::ADMIN),
            'code' => RoleEnum::ADMIN,
        ]);
        $this->webUserRole = Role::factory()->create([
            'name' => RoleEnum::getDescription(RoleEnum::WEB_USER),
            'code' => RoleEnum::WEB_USER,
        ]);
        $this->mobileUserRole = Role::factory()->create([
            'name' => RoleEnum::getDescription(RoleEnum::MOBILE_USER),
            'code' => RoleEnum::MOBILE_USER,
        ]);

        // ユーザーの作成
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id
        ]);
        $this->webUser = User::factory()->create([
            'role_id' => $this->webUserRole->id
        ]);
        $this->mobileUser = User::factory()->create([
            'role_id' => $this->mobileUserRole->id
        ]);
    }

    protected function setUpApplication(): self
    {
        $this->markClassTrueApplication = Application::factory()->create([
            'mark_class' => true,
            'account_class' => false,
            'notice_class' => false,
        ]);

        $this->accountClassTrueApplication = Application::factory()->create([
            'mark_class' => false,
            'account_class' => true,
            'notice_class' => false,
        ]);

        $this->noticeClassTrueApplication = Application::factory()->create([
            'mark_class' => false,
            'account_class' => false,
            'notice_class' => true,
        ]);
        return $this;
    }
}
