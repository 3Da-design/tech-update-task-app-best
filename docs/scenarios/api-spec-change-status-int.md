# シナリオ: API 仕様変更（status を整数化）

## 目的

REST API の `status` を **文字列**（`todo` / `in_progress` / `done`）から **整数**（`0` / `1` / `2`）に変更し、修正がどの層に波及するかを記録する。

> Web（Blade フォーム）は文字列のまま維持し、**API の入出力のみ**整数に変更する想定です。

## 想定される修正箇所（本リポジトリ）

| ファイル | 内容 |
|----------|------|
| `TaskInputNormalizer` | API 整数 → DB 文字列への変換 |
| `TaskResource` | DB 文字列 → API 整数への変換 |
| `TaskPayloadRequest` 継承先（API 用） | `Rule::in([0, 1, 2])` 等 |
| `TaskApiTest` | リクエスト / レスポンス期待値 |
| Postman コレクション | body / テストスクリプト |

Controller（`API\TaskController`）は **原則変更不要** です。

## マッピング例

| 整数（API） | 文字列（DB / Web） |
|-------------|-------------------|
| `0` | `todo` |
| `1` | `in_progress` |
| `2` | `done` |

## 事前条件

- `experiment-baseline-v1` タグまたは CI 緑の main
- `baseline` メトリクス取得済み（[BEFORE.md](../BEFORE.md)）

## 実施手順

```bash
git checkout -b exp/best-api-spec-change-status-int experiment-baseline-v1

# TaskInputNormalizer / TaskResource / FormRequest / API テストを更新
# （テスト・Postman はまだ触らない場合も after_update で失敗想定）

composer experiment:metrics -- --phase after_update --diff-ref experiment-baseline-v1

./scripts/check-quality.sh
composer experiment:metrics -- --phase after_fix --diff-ref experiment-baseline-v1

composer experiment:record -- --scenario api-spec-change-status-int --write
./scripts/publish-experiment-results.sh --scenario api-spec-change-status-int
```

## 完了条件

- [ ] CI 4 ジョブすべて成功（`after_fix`）
- [ ] `experiment/results/api-spec-change-status-int/` に 3 フェーズ JSON
