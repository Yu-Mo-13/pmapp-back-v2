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