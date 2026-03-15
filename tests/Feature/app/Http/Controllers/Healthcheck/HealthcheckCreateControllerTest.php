<?php

namespace Tests\Feature\app\Http\Controllers\Healthcheck;

use App\Models\Healthcheck;
use Tests\PmappTestCase;

class HealthcheckCreateControllerTest extends PmappTestCase
{
    public function test_認証なしでヘルスチェックレコードを作成できる(): void
    {
        $response = $this->postJson(route('healthchecks.create'));

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseCount('healthchecks', 1);

        $healthcheck = Healthcheck::first();
        $response->assertJson([
            'id' => $healthcheck->id,
        ]);
    }
}
