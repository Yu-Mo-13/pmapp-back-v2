<?php

namespace Tests\Feature\app\Http\Controllers\UnregistedPassword;

use App\Models\Account;
use App\Models\Application;
use App\Models\UnregistedPassword;
use Illuminate\Support\Str;
use Tests\PmappTestCase;

class UnregistedPasswordDeleteControllerTest extends PmappTestCase
{
    private UnregistedPassword $unregistedPassword;

    protected function setUp(): void
    {
        parent::setUp();

        $application = Application::factory()->create();
        $account = Account::factory()->create([
            'application_id' => $application->id,
        ]);

        $this->unregistedPassword = UnregistedPassword::factory()->create([
            'application_id' => $application->id,
            'account_id' => $account->id,
        ]);
    }

    public function test_正常に個別削除できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->deleteJson(route('unregisted-passwords.delete', [
            'unregistedPassword' => $this->unregistedPassword->uuid,
        ]));

        $response->assertStatus(200);
        $response->assertJson([]);
        $this->assertDatabaseMissing('unregisted_passwords', [
            'uuid' => $this->unregistedPassword->uuid,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->deleteJson(route('unregisted-passwords.delete', [
            'unregistedPassword' => $this->unregistedPassword->uuid,
        ]));

        $response->assertStatus(401);
    }

    public function test_存在しないレコード指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->deleteJson(route('unregisted-passwords.delete', [
            'unregistedPassword' => (string) Str::uuid(),
        ]));

        $response->assertStatus(404);
    }
}
