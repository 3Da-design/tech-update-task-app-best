# シナリオ: DB / クエリ変更（タイトル検索の大文字小文字）

## 目的

検索クエリの変更が **1 箇所に集約されるか** を記録する。本リポジトリでは [`TaskListQuery`](../../../app/Services/Task/TaskListQuery.php) のみを修正します。

## 想定される修正箇所

| ファイル | 内容 |
|----------|------|
| `TaskListQuery` | `LOWER(title) LIKE` による case-insensitive 検索 |
| `tests/Feature/TaskListFilterTest.php` | 新規テスト追加 |

## 事前条件

- `experiment-baseline-v1` タグ（CI 緑）
- PostgreSQL で実行（`LIKE` の挙動を CI と一致させる）
- `baseline` メトリクス取得済み

## 変更内容

### 1. テスト追加

`TaskListFilterTest` に case-insensitive 検索テストを追加（`after_update` で失敗想定）。

### 2. クエリ変更

`TaskListQuery::forUser` 内のタイトル検索を以下に変更:

```php
$query->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($this->escapeLike($title)).'%']);
```

## 実施手順

```bash
git checkout -b exp/best-db-schema-change experiment-baseline-v1

composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1

# テスト追加 → after_update
composer experiment:metrics -- --phase after_update --diff-ref experiment-baseline-v1

# TaskListQuery を修正 → CI 緑
./scripts/check-quality.sh
composer experiment:metrics -- --phase after_fix --diff-ref experiment-baseline-v1

./scripts/publish-experiment-results.sh --scenario db-schema-change
```

## 完了条件

- [ ] CI 4 ジョブすべて成功
- [ ] `docs/experiment/results/db-schema-change/` に 3 フェーズ JSON
- [ ] [RESULTS.md](../results/RESULTS.md) を更新
