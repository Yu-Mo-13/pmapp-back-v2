<?php

namespace Tests\Feature\app\Http\Controllers\Healthcheck;

use App\Models\Healthcheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\PmappTestCase;

class HealthcheckStatusShowControllerTest extends PmappTestCase
{
    public function test_当日のレコードがあれば成功メッセージを返す(): void
    {
        $healthcheck = Healthcheck::create([
            'created_at' => Carbon::today()->setHour(9),
            'updated_at' => Carbon::today()->setHour(9),
        ]);

        $response = $this->getJson(route('healthchecks.status'));

        $response->assertOk()
            ->assertJson([
                'is_healthy' => true,
                'message' => 'Healthcheck succeeded.',
                'id' => $healthcheck->id,
            ]);
    }

    public function test_未ログインでも当日のレコードがなければ失敗メッセージを含む200を返す(): void
    {
        Healthcheck::create([
            'created_at' => Carbon::yesterday()->setHour(23),
            'updated_at' => Carbon::yesterday()->setHour(23),
        ]);

        $response = $this->getJson(route('healthchecks.status'));

        $response->assertOk()
            ->assertJson([
                'is_healthy' => false,
                'message' => 'Healthcheck failed: no record found for today.',
            ]);
    }

    public function test_レコード確認時に例外が発生しても失敗メッセージを含む200を返す(): void
    {
        Schema::dropIfExists('healthchecks');

        $response = $this->getJson(route('healthchecks.status'));

        $response->assertOk()
            ->assertJson([
                'is_healthy' => false,
                'message' => 'Healthcheck failed.',
            ]);
    }
}
