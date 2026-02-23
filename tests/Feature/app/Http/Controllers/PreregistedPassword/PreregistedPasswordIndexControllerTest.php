<?php

namespace Tests\Feature\app\Http\Controllers\PreregistedPassword;

use App\Models\Account;
use App\Models\Application;
use App\Models\PreregistedPassword;
use Tests\PmappTestCase;

class PreregistedPasswordIndexControllerTest extends PmappTestCase
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

        $response = $this->getJson(route('preregisted-passwords.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'uuid',
                'application' => ['id', 'name'],
                'account' => ['id', 'name'],
                'created_at',
            ],
        ]);

        $response->assertJsonFragment([
            'uuid' => $this->preregistedPassword->uuid,
        ]);

        $responseData = $response->json();
        $this->assertNotEmpty($responseData);
        $this->assertArrayNotHasKey('password', $responseData[0]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('preregisted-passwords.index'));
        $response->assertStatus(401);
    }
}
