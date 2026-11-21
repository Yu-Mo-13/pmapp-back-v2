<?php

namespace Tests\Feature\App\Http\Controllers\User;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class UserIndexControllerTest extends TestCase
{
    public function test_合言葉が正しければマスクしたユーザー一覧をダウンロードできる(): void
    {
        config(['app.countersign' => 'test-countersign']);
        $role = Role::factory()->create(['name' => '管理者']);
        User::factory()->withRole($role->id)->create([
            'name' => '田中, 太郎',
            'email' => 'alice@example.com',
            'uid' => '12345678',
        ]);
        User::factory()->withRole($role->id)->create([
            'name' => '削除済みユーザー',
            'email' => 'a@b.c',
            'uid' => null,
            'deleted_at' => '2026-01-02 03:04:05',
        ]);

        $response = $this->get('/api/v2/users', ['X-Countersign' => 'test-countersign']);

        $response->assertOk()->assertDownload('users.csv');
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertSame(
            "ユーザー名,メールアドレス,ロール名,\"Supabase UID\",削除日時\n"
                . "\"田中, 太郎\",alice************,管理者,12345***,\n"
                . "削除済みユーザー,a@b.c,管理者,,\"2026-01-02 03:04:05\"\n",
            $response->streamedContent()
        );
    }

    /**
     * @dataProvider invalidCountersigns
     */
    public function test_合言葉が無効ならバリデーションエラーを返す(array $headers, ?string $countersign): void
    {
        config(['app.countersign' => $countersign]);

        $this->get('/api/v2/users', $headers)
            ->assertStatus(422)
            ->assertJsonPath('errors.countersign.0', '合言葉を指定してください。');
    }

    public function invalidCountersigns(): array
    {
        return [
            'missing' => [[], 'test-countersign'],
            'incorrect' => [['X-Countersign' => 'wrong'], 'test-countersign'],
            'unconfigured' => [['X-Countersign' => 'test-countersign'], null],
            'empty' => [['X-Countersign' => ''], ''],
        ];
    }
}
