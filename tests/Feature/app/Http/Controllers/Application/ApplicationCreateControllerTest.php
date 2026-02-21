<?php

namespace Tests\Feature\app\Http\Controllers\Application;

use Tests\PmappTestCase;

class ApplicationCreateControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApplication();
    }

    public function test_アプリケーションが正常に作成できること()
    {
        $this->actingAs($this->adminUser, 'api');
        $testApplication = [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ];

        $response = $this->postJson(route('applications.create'), $testApplication);
        $response->assertOk();
        $this->assertDatabaseHas('applications', $testApplication['application']);
    }

    public function test_アプリケーション名が未入力の場合、バリデーションエラーになること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => '',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.name']);
    }

    public function test_アプリケーション名が文字列でない場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 123,
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.name']);
    }

    public function test_アプリケーション名が255文字を超える場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $longName = str_repeat('a', 256);
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => $longName,
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.name']);
    }

    public function test_アプリケーション名が重複する場合、バリデーションエラーとなること()
    {
        $existingApplication = $this->accountClassTrueApplication;
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => $existingApplication->name,
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.name']);
    }

    public function test_アカウント区分が未入力の場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => null,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.account_class']);
    }

    public function test_アカウント区分がboolean型でない場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => 'yes',
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.account_class']);
    }

    public function test_定期通知区分が未入力の場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => null,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.notice_class']);
    }

    public function test_定期通知区分がboolean型でない場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => 'yes',
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.notice_class']);
    }

    public function test_記号区分が未入力の場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => null,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.mark_class']);
    }

    public function test_記号区分がboolean型でない場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => 'yes',
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.mark_class']);
    }

    public function test_仮登録パスワード桁数が未入力の場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => null
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.pre_password_size']);
    }

    public function test_仮登録パスワード桁数が整数でない場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 'eight'
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.pre_password_size']);
    }

    public function test_仮登録パスワード桁数が1未満の場合、バリデーションエラーとなること()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 0
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application.pre_password_size']);
    }

    public function test_未ログインの場合、アプリケーションの作成ができないこと()
    {
        $response = $this->postJson(route('applications.create'), [
            'application' => [
                'name' => 'Test Application',
                'account_class' => true,
                'notice_class' => true,
                'mark_class' => true,
                'pre_password_size' => 8
            ]
        ]);

        $response->assertStatus(401);
    }
}
