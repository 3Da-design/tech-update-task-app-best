# tech-update-task-app-best

Laravel 製のタスク管理アプリです。Controller / Service / Policy に責務を分離した構成で、Web（Blade）と REST API の両方を提供します。

[![CI](https://github.com/3Da-design/tech-update-task-app-best/actions/workflows/ci.yml/badge.svg)](https://github.com/3Da-design/tech-update-task-app-best/actions/workflows/ci.yml)

---

## 機能

- ユーザー登録・ログイン（Laravel Breeze）
- タスクの CRUD（一覧フィルタ・ソート付き）
- REST API（認証付き JSON）
- PHPStan / ESLint / PHPUnit / Newman による品質チェック

詳細は [docs/FEATURE_LIST.md](docs/FEATURE_LIST.md) を参照してください。

---

## 技術スタック

| 区分 | 技術 |
|------|------|
| バックエンド | Laravel 13、PHP 8.4 |
| 認証 | Laravel Breeze（セッション） |
| DB | PostgreSQL（Docker Compose） |
| フロント | Blade、Tailwind CSS、Vite、Alpine.js |
| 品質 | PHPStan (Larastan)、Laravel Pint、ESLint |
| テスト | PHPUnit、Postman / Newman |
| CI | GitHub Actions（4 ジョブ並列） |

---

## クイックスタート

### 前提

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) など Compose v2 対応環境
- 開発は **Docker Compose のみ**（Web **8003**、DB 公開 **5434**）
- フロント（npm）は Docker の `node` サービスのみ（ホストで `npm install` しない）

### 初回セットアップ

```bash
cp .env.example .env

docker compose build app
docker compose up -d

docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

composer npm:docker-build
```

ブラウザで `http://localhost:8003` を開きます。

シードユーザー: `test@example.com` / `password`

### よく使うコマンド

```bash
docker compose logs -f
docker compose down              # DB ボリュームは残る
docker compose down -v           # DB ごと削除

./scripts/check-quality.sh       # PHPStan → ESLint → build → PHPUnit → Newman
./scripts/curl-api-smoke.sh      # API 疎通確認
```

---

## アーキテクチャ

```text
HTTP (Web / API)
    │
    ▼
TaskController (thin)        … TaskService へ委譲、TaskPolicy で認可
    │
    ▼
TaskService                  … ユースケース境界
    │
    ├── TaskListQuery        … 一覧クエリ
    ├── TaskInputNormalizer  … 入力正規化
    └── Task (Model)
```

| レイヤ | 主なクラス |
|--------|-----------|
| Controller | `Web\TaskController`, `API\TaskController`, `FindsAuthorizedTask` |
| 認可 | `TaskPolicy` |
| ユースケース | `TaskService` |
| クエリ / 正規化 | `TaskListQuery`, `TaskInputNormalizer`, `TaskListFilters` |
| 入出力 | `TaskPayloadRequest`, `TaskResource` |

---

## テストと CI

```bash
./scripts/check-quality.sh

# 個別
docker compose exec app composer phpstan
docker compose exec app composer test
docker compose --profile node run --rm node npm run test:api
```

| ジョブ | 内容 |
|--------|------|
| `php-tests` | PHPUnit（事前に Vite build） |
| `php-quality` | Pint + PHPStan |
| `frontend` | ESLint + Vite build |
| `api-tests` | Newman |

詳細: [docs/TESTING.md](docs/TESTING.md)、[docs/CI.md](docs/CI.md)

---

## 技術更新実験（任意）

技術更新時の影響をメトリクスで記録する手順があります。

| ドキュメント | 内容 |
|--------------|------|
| [docs/EXPERIMENT.md](docs/EXPERIMENT.md) | 実験設計・評価指標 |
| [docs/experiment/BEFORE.md](docs/experiment/BEFORE.md) | ベースライン手順 |
| [docs/experiment/scenarios/](docs/experiment/scenarios/) | 更新シナリオ（3 件） |
| [docs/experiment/results/RESULTS.md](docs/experiment/results/RESULTS.md) | 計測結果一覧 |

```bash
composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1
./scripts/publish-experiment-results.sh --scenario api-spec-change-status-int
```

---

## ドキュメント

| ドキュメント | 内容 |
|--------------|------|
| [docs/FEATURE_LIST.md](docs/FEATURE_LIST.md) | 機能一覧 |
| [docs/TESTING.md](docs/TESTING.md) | テストの使い方 |
| [docs/CI.md](docs/CI.md) | GitHub Actions |
| [postman/README.md](postman/README.md) | Postman コレクション |

---

## ライセンス

MIT（Laravel プロジェクトスケルトンに準拠）
