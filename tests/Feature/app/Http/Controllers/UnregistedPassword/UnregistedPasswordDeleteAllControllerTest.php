<?php

namespace Tests\Feature\app\Http\Controllers\UnregistedPassword;

use App\Models\Account;
use App\Models\Application;
use App\Models\UnregistedPassword;
use Tests\PmappTestCase;

class UnregistedPasswordDeleteAllControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $application = Application::factory()->create();
        $account = Account::factory()->create([
            'application_id' => $application->id,
        ]);

        UnregistedPassword::factory()->count(3)->create([
            'application_id' => $application->id,
            'account_id' => $account->id,
        ]);
    }

    public function test_正常に全件削除できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->deleteJson(route('unregisted-passwords.delete-all'));
        $response->assertStatus(200);
        $this->assertDatabaseCount('unregisted_passwords', 0);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->deleteJson(route('unregisted-passwords.delete-all'));
        $response->assertStatus(401);
    }
}
