# シナリオ: API 仕様変更（priority 追加）

## 目的

REST API に `priority` フィールド（`low` / `medium` / `high`）を追加し、修正がどの層に波及するかを記録する。

## 想定される修正箇所（本リポジトリ）

| ファイル | 内容 |
|----------|------|
| migration | `tasks.priority` カラム追加 |
| `TaskInputNormalizer` | `priority` の正規化 |
| `TaskPayloadRequest` 継承先 | バリデーションルール |
| `TaskResource` | JSON 出力 |
| `tasks/_form.blade.php` | select 追加（Web parity） |
| `TaskApiTest` / `TaskWebTest` / Postman | 期待値更新 |

Controller は **原則変更不要** です。

## 事前条件

- `experiment-baseline-v1` タグまたは CI 緑の main
- `baseline` メトリクス取得済み（[BEFORE.md](../BEFORE.md)）

## 変更内容

1. `tasks` に `priority` カラム（string, default `medium`）
2. `TaskResource` に `priority` を追加
3. FormRequest に `Rule::in(['low', 'medium', 'high'])` を追加
4. `TaskInputNormalizer` に `priority` を追加
5. テスト・Postman・Blade フォームを更新

## 実施手順

```bash
git checkout -b exp/best-api-spec-change-priority experiment-baseline-v1

docker compose exec app php artisan make:migration add_priority_to_tasks_table
docker compose exec app php artisan migrate

composer experiment:metrics -- --phase after_update --diff-ref experiment-baseline-v1

./scripts/check-quality.sh
composer experiment:metrics -- --phase after_fix --diff-ref experiment-baseline-v1

composer experiment:record -- --scenario api-spec-change-priority --write
./scripts/publish-experiment-results.sh --scenario api-spec-change-priority
```

## 完了条件

- [ ] CI 4 ジョブすべて成功（`after_fix`）
- [ ] `docs/experiment/results/api-spec-change-priority/` に 3 フェーズ JSON
- [ ] [RESULTS.md](../results/RESULTS.md) を更新
