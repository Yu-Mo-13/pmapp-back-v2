# pmapp-back-v2
PMAPPのバックエンドAPI ver.2

## 🛠️ 使用技術
- **言語**: PHP 8.2
- **フレームワーク**: Laravel 8
- **データベース**: PostgreSQL 13
- **キャッシュ**: Redis 7
- **Webサーバー**: Nginx
- **コンテナ**: Docker
- **DB管理**: pgAdmin 4
- **APIドキュメント**: OpenAPI 3.0
- **テスト**: PHPUnit
- **CI/CD**: GitHub Actions
- **コードレビュー**: ReviewDog
- **静的解析**: PHPStan/Larastan
- **コードスタイル**: PHP_CodeSniffer (PSR-12)

# 開発環境構築

## 前提条件
- Docker
- Docker Compose

## セットアップ

1. リポジトリをクローン
```bash
git clone <repository-url>
cd pmapp-back-v2
```

2. Docker環境を起動
```bash
docker-compose up -d --build
```

3. Composerの依存関係をインストール（既に完了済み）
```bash
docker-compose exec app composer install
```

4. アプリケーションキーを生成（既に完了済み）
```bash
docker-compose exec app php artisan key:generate
```

5. データベースマイグレーション
```bash
docker-compose exec app php artisan migrate
```

## アクセス

| サービス | URL/接続先 | 説明 |
|---------|-----------|------|
| アプリケーション | http://localhost:8080 | Laravel アプリケーション |
| pgAdmin | http://localhost:8081 | PostgreSQL データベース管理ツール |
| PostgreSQL | localhost:5432 | データベース直接接続 |
| Redis | localhost:6379 | Redis接続 |

## 🗄️ データベース接続手順

### PostgreSQL接続情報
```
Host: localhost
Port: 5432
Database: pmapp_db
Username: pmapp_user
Password: pmapp_password
```

### pgAdminでのデータベース管理

1. **pgAdminにアクセス**
   - URL: http://localhost:8081
   - Email: `admin@example.com`
   - Password: `admin`

2. **PostgreSQLサーバーの登録**
   - 左メニューで "Servers" を右クリック → "Register" → "Server..."
   
   **General タブ**:
   - Name: `pmapp-db` (任意の名前)
   
   **Connection タブ**:
   - Host name/address: `db` (Docker内部のサービス名)
   - Port: `5432`
   - Maintenance database: `pmapp_db`
   - Username: `pmapp_user`
   - Password: `pmapp_password`

3. **接続完了**
   - "Save" をクリックしてサーバー登録完了
   - データベースの中身を視覚的に確認・操作可能

### 外部DBクライアントでの接続

DBeaver、TablePlus、DataGripなどの外部DBクライアントからも接続可能です：
- 上記のPostgreSQL接続情報を使用してください

## 開発用コマンド

```bash
# コンテナの状況確認
docker compose ps

# コンテナログの確認
docker compose logs app
docker compose logs db
docker compose logs pgadmin

# アプリケーションコンテナに接続
docker compose exec app bash

# データベースに直接接続
docker compose exec db psql -U pmapp_user -d pmapp_db

# Artisanコマンド実行
docker compose exec app php artisan <command>

# マイグレーション実行
docker compose exec app php artisan migrate

# テスト実行
docker compose exec app php artisan test

# サービス停止
docker compose down

# データを含めて完全削除
docker compose down -v
```

## 🔍 コード品質チェック

### ローカルでのテスト実行

```bash
# ユニットテスト実行
docker compose exec app composer test

# カバレッジ付きテスト実行
docker compose exec app composer test-coverage

# PHP CodeSniffer実行
docker compose exec app composer phpcs

# コーディング規約の自動修正
docker compose exec app composer phpcs-fix

# Larastan（静的解析）実行
docker compose exec app composer larastan
```

### CI/CDワークフロー

GitHub Actionsで以下が自動実行されます：

#### 📋 テストワークフロー (`.github/workflows/tests.yml`)
- **マルチPHPバージョン対応**: PHP 8.1, 8.2でテスト実行
- **データベーステスト**: PostgreSQL, Redis環境でのテスト
- **マイグレーション**: 自動的にデータベースマイグレーション実行
- **カバレッジ**: Codecov によるテストカバレッジ測定

#### 🔍 コード品質ワークフロー (`.github/workflows/code-quality.yml`)
**統合されたコード品質チェック - 重複を排除した効率的な構成**

##### Push時（mainブランチ）
- **全体スキャン**: プロジェクト全体のPHPCS・PHPStan実行
- **GitHub形式出力**: 問題箇所の直接表示
- **セキュリティ監査**: 依存関係の脆弱性チェック

##### Pull Request時
- **差分ベースチェック**: 変更されたファイルのみを効率的にチェック
- **ReviewDog統合**: PRコメントでのリアルタイムフィードバック
- **段階的レビュー**: warning（PHPCS）とerror（PHPStan）の適切な分類
- **フィルタリング**: 変更箇所のコンテキストのみに焦点

#### 🛠️ 統合された機能
- **コードスタイル**: PSR-12準拠の自動チェック（PHPCS）
- **静的解析**: Laravel特化の型安全性チェック（PHPStan/Larastan）
- **セキュリティ**: Composer audit による脆弱性検出
- **アーティファクト**: 解析結果の自動保存

### 🎯 コード品質管理

#### ReviewDog機能
- **インラインコメント**: コード行に直接コメント
- **差分フィルタリング**: 変更箇所のみをチェック
- **重要度レベル**: error, warning での適切な分類
- **レポーター**: github-pr-review形式での詳細フィードバック

#### 自動化されたワークフロー最適化
- **重複排除**: 1つのワークフローで全コード品質チェックを統合
- **条件分岐**: Push/PR時で異なる実行戦略
- **効率性**: 変更ファイルのみの処理によるCI時間短縮
- **一貫性**: 統一されたコーディング標準の適用

### 設定ファイル

#### コード品質設定
- `phpcs.xml`: PHP CodeSniffer設定（PSR-12準拠）
- `phpstan.neon`: Larastan設定（レベル5）

#### CI/CD設定
- `.github/workflows/tests.yml`: PHPUnitテスト実行ワークフロー
- `.github/workflows/code-quality.yml`: 統合コード品質チェックワークフロー
  - PHPCS（コードスタイル）
  - PHPStan（静的解析）
  - ReviewDog（自動レビュー）
  - セキュリティ監査
- `.github/scripts/reviewdog-wrapper.sh`: ReviewDog安全実行スクリプト
