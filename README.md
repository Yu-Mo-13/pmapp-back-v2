# pmapp-back-v2
PMAPPのバックエンドAPI ver.2

# 使用技術
- 言語：PHP 8
- フレームワーク：Laravel 8
- データベース：PostgreSQL 13
- キャッシュ：Redis
- コンテナ：Docker
- APIドキュメント：OpenAPI 3.0
- テスト：PHPUnit
- CI/CD：GitHub Actions
- その他：Swagger

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

- アプリケーション: http://localhost:8080
- PostgreSQL: localhost:5432
- Redis: localhost:6379

## 開発用コマンド

```bash
# コンテナログの確認
docker-compose logs

# アプリケーションコンテナに接続
docker-compose exec app bash

# Artisanコマンド実行
docker-compose exec app php artisan <command>

# テスト実行
docker-compose exec app php artisan test
```