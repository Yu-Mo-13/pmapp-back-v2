<?php

namespace Tests\Feature\app\Http\Controllers\Application;

use Tests\PmappTestCase;

class ApplicationIndexControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApplication();
    }

    public function test_レスポンス形式の確認()
    {
        $response = $this->get(route('applications.index'));

        $response->assertStatus(200);

        // 応用のリストが返されることを確認
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'account_class',
                'notice_class',
                'mark_class'
            ]
        ]);

        // レスポンスが配列であることを確認
        $responseData = $response->json();
        $this->assertIsArray($responseData);
        $this->assertGreaterThan(0, count($responseData));

        // 各アプリケーションの構造を確認
        foreach ($responseData as $application) {
            $this->assertArrayHasKey('id', $application);
            $this->assertArrayHasKey('name', $application);
            $this->assertArrayHasKey('account_class', $application);
            $this->assertArrayHasKey('notice_class', $application);
            $this->assertArrayHasKey('mark_class', $application);

            // 型の確認
            $this->assertIsInt($application['id']);
            $this->assertIsString($application['name']);
            $this->assertIsInt($application['account_class']);
            $this->assertIsInt($application['notice_class']);
            $this->assertIsInt($application['mark_class']);
        }
    }

    public function test_テストデータが全てレスポンスに含まれていることを確認(): void
    {
        $response = $this->get(route('applications.index'));
        $response->assertStatus(200);

        $this->assertDatabaseHas('applications', [
            'id' => $this->accountClassTrueApplication->id,
            'name' => $this->accountClassTrueApplication->name,
            'account_class' => true,
            'notice_class' => false,
            'mark_class' => false,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->markClassTrueApplication->id,
            'name' => $this->markClassTrueApplication->name,
            'account_class' => false,
            'notice_class' => false,
            'mark_class' => true,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->noticeClassTrueApplication->id,
            'name' => $this->noticeClassTrueApplication->name,
            'account_class' => false,
            'notice_class' => true,
            'mark_class' => false,
        ]);
    }
}
