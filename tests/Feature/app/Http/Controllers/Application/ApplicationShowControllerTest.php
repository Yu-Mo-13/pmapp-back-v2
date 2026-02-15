<?php

namespace Tests\Feature\app\Http\Controllers\Application;

use Tests\PmappTestCase;

class ApplicationShowControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApplication();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_レスポンス形式の確認()
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->getJson(route('applications.show', ['application' => $this->markClassTrueApplication]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'account_class',
            'notice_class',
            'mark_class',
            'pre_password_size'
        ]);
    }

    public function test_テストデータがレスポンスに含まれていることを確認(): void
    {
        $this->actingAs($this->adminUser, 'api');
        $response = $this->getJson(route('applications.show', ['application' => $this->markClassTrueApplication]));
        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertEquals($this->markClassTrueApplication->id, $responseData['id']);
        $this->assertEquals($this->markClassTrueApplication->name, $responseData['name']);
        $this->assertEquals($this->markClassTrueApplication->account_class, $responseData['account_class']);
        $this->assertEquals($this->markClassTrueApplication->notice_class, $responseData['notice_class']);
        $this->assertEquals($this->markClassTrueApplication->mark_class, $responseData['mark_class']);
        $this->assertEquals($this->markClassTrueApplication->pre_password_size, $responseData['pre_password_size']);
    }

    public function test_存在しないアプリケーションIDを指定した場合は404エラー(): void
    {
        $nonExistentId = 9999; // 存在しないIDを指定
        $this->actingAs($this->adminUser, 'api');
        $response = $this->getJson(route('applications.show', ['application' => $nonExistentId]));
        $response->assertStatus(404);
    }

    public function test_認証されていない場合は401エラー(): void
    {
        $response = $this->getJson(route('applications.show', ['application' => $this->markClassTrueApplication]));
        $response->assertStatus(401);
    }
}
