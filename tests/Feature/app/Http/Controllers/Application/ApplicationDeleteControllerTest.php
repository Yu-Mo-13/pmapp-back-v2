<?php

namespace Tests\Feature\app\Http\Controllers\Application;

use Tests\PmappTestCase;

class ApplicationDeleteControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApplication();
    }

    public function test_アプリケーションが正常に削除できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->deleteJson(route('applications.delete', ['application' => $this->accountClassTrueApplication->id]));

        $response->assertOk();
        $this->assertSoftDeleted('applications', [
            'id' => $this->accountClassTrueApplication->id,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->deleteJson(route('applications.delete', ['application' => $this->accountClassTrueApplication->id]));

        $response->assertStatus(401);
    }

    public function test_存在しないアプリケーションID指定時は404になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->deleteJson(route('applications.delete', ['application' => 999999]));

        $response->assertStatus(404);
    }
}
