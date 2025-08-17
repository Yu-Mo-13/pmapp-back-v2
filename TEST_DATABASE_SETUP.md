# テストデータベースセットアップガイド

このプロジェクトのテスト用データベース環境が正常にセットアップされています。

## 設定概要

### データベース構成
- **テスト用データベース**: SQLite（メモリ内実行）
- **本番用データベース**: MySQL/PostgreSQL（設定による）
- **テスト実行時の自動化**: マイグレーション自動実行、データベースリフレッシュ

### ファイル構成

#### テスト設定
- `phpunit.xml`: テスト用データベースの設定（SQLiteメモリ内DB使用）
- `tests/TestCase.php`: 基本テストクラス（RefreshDatabase使用）
- `tests/TestCaseWithSeeder.php`: シーダー付きテストクラス

#### テストファイル
- `tests/Feature/DatabaseTestSetupTest.php`: データベース機能の動作確認テスト

#### ファクトリー
- `database/factories/UserFactory.php`: ユーザーテストデータ生成
- `database/factories/RoleFactory.php`: ロールテストデータ生成
- `database/factories/ApplicationFactory.php`: アプリケーションテストデータ生成

#### シーダー
- `database/seeders/TestSeeder.php`: テスト用データの一括投入

## 使用方法

### 1. 基本的なテスト実行

```bash
# 全テスト実行
php artisan test

# 特定のテスト実行
php artisan test tests/Feature/DatabaseTestSetupTest.php

# テスト失敗時に停止
php artisan test --stop-on-failure
```

### 2. テストクラスの書き方

#### 基本パターン（データベース自動リフレッシュ）
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class MyTest extends TestCase
{
    public function test_example()
    {
        $user = User::factory()->create();
        
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }
}
```

#### シーダー付きパターン
```php
<?php

namespace Tests\Feature;

use Tests\TestCaseWithSeeder;

class MyTestWithSeeder extends TestCaseWithSeeder
{
    public function test_with_seed_data()
    {
        // シーダーによって作成されたデータが利用可能
        $this->assertDatabaseCount('roles', 3);
    }
}
```

### 3. ファクトリーの使用例

```php
// 基本的な作成
$user = User::factory()->create();

// 特定の値で作成
$user = User::factory()->create([
    'email' => 'test@example.com',
]);

// 複数作成
$users = User::factory()->count(5)->create();

// 関連データ付きで作成
$role = Role::factory()->create();
$user = User::factory()->create(['role_id' => $role->id]);
```

### 4. データベースアサーション

```php
// データの存在確認
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);

// データの不存在確認
$this->assertDatabaseMissing('users', ['email' => 'missing@example.com']);

// レコード数の確認
$this->assertDatabaseCount('users', 5);
```

## モデル関係

### User
- `name`: ユーザー名
- `email`: メールアドレス
- `role_id`: ロールID（外部キー）

### Role
- `name`: ロール名
- `code`: ロールコード

### Application
- `name`: アプリケーション名
- `account_class`: アカウント区分
- `notice_class`: 変更通知区分
- `mark_class`: 記号区分
- `pre_password_size`: 仮登録パスワード桁数

## 設定の変更

### データベース設定の変更
`phpunit.xml`でテスト用データベースの設定を変更できます：

```xml
<php>
    <!-- SQLiteファイルを使用する場合 -->
    <server name="DB_CONNECTION" value="sqlite"/>
    <server name="DB_DATABASE" value="database/testing.sqlite"/>
    
    <!-- MySQLテスト用データベースを使用する場合 -->
    <server name="DB_CONNECTION" value="mysql"/>
    <server name="DB_DATABASE" value="test_database"/>
</php>
```

### テストシーダーの実行
シーダー付きでテストする場合は`TestCaseWithSeeder`を継承：

```php
use Tests\TestCaseWithSeeder;

class MyTest extends TestCaseWithSeeder
{
    // シーダーが自動実行される
}
```

## トラブルシューティング

### テスト実行時のエラー
1. **マイグレーションエラー**: `php artisan migrate:status`で確認
2. **ファクトリーエラー**: モデルとファクトリーのフィールド一致を確認
3. **権限エラー**: `storage/`ディレクトリの書き込み権限を確認

### パフォーマンス最適化
- 大量データのテスト時は`RefreshDatabase`の代わりに`DatabaseTransactions`を検討
- 不要なテストデータの作成を避ける
- ファクトリーの`make()`メソッドでDB保存なしのモデル作成

## 次のステップ

1. より複雑なテストシナリオの追加
2. API エンドポイントのテスト追加
3. フロントエンドとの統合テスト
4. CI/CD パイプラインでのテスト自動化
