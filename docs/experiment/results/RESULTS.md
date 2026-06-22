# 実験結果（tech-update-task-app-best）

本リポジトリで計測したメトリクスの一覧です。

## シナリオ一覧

| ID | 内容 | 状態 | ディレクトリ |
|----|------|------|--------------|
| `api-spec-change-status-int` | API の `status` を整数化 | 未実施 | [api-spec-change-status-int/](./api-spec-change-status-int/) |
| `api-spec-change-priority` | `priority` フィールド追加 | 未実施 | [api-spec-change-priority/](./api-spec-change-priority/) |
| `db-schema-change` | タイトル検索の大文字小文字を区別しない | 未実施 | [db-schema-change/](./db-schema-change/) |

> 実施後: `./scripts/publish-experiment-results.sh --scenario <id>` で JSON を公開し、上表の「状態」を更新してください。

## フェーズ

| フェーズ | 意味 |
|----------|------|
| `baseline` | 更新前（CI 緑） |
| `after_update` | 更新直後（テスト失敗を含む） |
| `after_fix` | 修正完了（CI 緑） |

主指標は **`after_fix` の `git.files_changed`** です。詳細は [EXPERIMENT.md](../EXPERIMENT.md)。

## 公開手順

```bash
composer experiment:metrics -- --phase after_fix --diff-ref experiment-baseline-v1
composer experiment:record -- --scenario api-spec-change-status-int --write
./scripts/publish-experiment-results.sh --scenario api-spec-change-status-int
```
