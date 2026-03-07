<?php

namespace Tests\Feature\app\Http\Controllers\Application;

use Tests\PmappTestCase;

class ApplicationUpdateControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApplication();
    }

    public function test_アプリケーションが正常に更新できること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $payload = [
            'application' => [
                'name' => 'Updated Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => false,
                'pre_password_size' => 12,
            ],
        ];

        $response = $this->putJson(route('applications.update', ['application' => $this->accountClassTrueApplication->id]), $payload);

        $response->assertOk();
        $this->assertDatabaseHas('applications', [
            'id' => $this->accountClassTrueApplication->id,
            'name' => 'Updated Application',
            'account_class' => true,
            'notice_class' => true,
            'mark_class' => false,
            'pre_password_size' => 12,
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->putJson(route('applications.update', ['application' => $this->accountClassTrueApplication->id]), [
            'application' => [
                'name' => 'Updated Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => false,
                'pre_password_size' => 12,
            ],
        ]);

        $response->assertStatus(401);
    }

    public function test_必須項目未指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('applications.update', ['application' => $this->accountClassTrueApplication->id]), [
            'application' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'application.name',
            'application.account_class',
            'application.notice_class',
            'application.mark_class',
            'application.pre_password_size',
        ]);
    }

    public function test_重複したアプリケーション名指定時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('applications.update', ['application' => $this->accountClassTrueApplication->id]), [
            'application' => [
                'name' => $this->markClassTrueApplication->name,
                'account_class' => true,
                'notice_class' => false,
                'mark_class' => false,
                'pre_password_size' => 8,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.name']);
    }

    public function test_仮登録パスワード桁数が1未満の時は422になること(): void
    {
        $this->actingAs($this->adminUser, 'api');

        $response = $this->putJson(route('applications.update', ['application' => $this->accountClassTrueApplication->id]), [
            'application' => [
                'name' => 'Updated Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 0,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.pre_password_size']);
    }
}
