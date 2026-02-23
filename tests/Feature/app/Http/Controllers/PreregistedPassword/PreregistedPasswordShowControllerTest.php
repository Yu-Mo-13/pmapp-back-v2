<?php

namespace Tests\Feature\app\Http\Controllers\PreregistedPassword;

use App\Models\Account;
use App\Models\Application;
use App\Models\PreregistedPassword;
use Illuminate\Support\Str;
use Tests\PmappTestCase;

class PreregistedPasswordShowControllerTest extends PmappTestCase
{
    private PreregistedPassword $preregistedPassword;

    protected function setUp(): void
    {
        parent::setUp();

        $application = Application::factory()->create();
        $account = Account::factory()->create([
            'application_id' => $application->id,
        ]);

        $this->preregistedPassword = PreregistedPassword::factory()->create([
            'application_id' => $application->id,
            'account_id' => $account->id,
            'password' => 'plain-password',
        ]);
    }

    public function test_正常取得できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.show', [
            'preregistedPassword' => $this->preregistedPassword->uuid,
        ]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'uuid',
            'password',
            'application' => ['id', 'name'],
            'account' => ['id', 'name'],
            'created_at',
        ]);
        $response->assertJsonFragment([
            'uuid' => $this->preregistedPassword->uuid,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('preregisted-passwords.show', [
            'preregistedPassword' => $this->preregistedPassword->uuid,
        ]));
        $response->assertStatus(401);
    }

    public function test_存在しないレコード指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->getJson(route('preregisted-passwords.show', [
            'preregistedPassword' => (string) Str::uuid(),
        ]));
        $response->assertStatus(404);
    }
}
