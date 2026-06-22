# 実験設計

本リポジトリ（`tech-update-task-app-best`）で、技術更新シナリオの影響をメトリクスとして記録するための手順です。

## 更新シナリオ（3 件）

| ID | ドキュメント |
|----|--------------|
| `api-spec-change-status-int` | [api-spec-change-status-int.md](./experiment/scenarios/api-spec-change-status-int.md) |
| `api-spec-change-priority` | [api-spec-change-priority.md](./experiment/scenarios/api-spec-change-priority.md) |
| `db-schema-change` | [db-schema-change.md](./experiment/scenarios/db-schema-change.md) |

## アーキテクチャ（計測対象）

- **Controller:** 薄い層。`TaskService` へ委譲。認可は `TaskPolicy` + `FindsAuthorizedTask`
- **Service:** `TaskService` がユースケース境界
- **協調クラス:** `TaskListQuery`、`TaskInputNormalizer`、`TaskListFilters`
- **入出力:** `TaskPayloadRequest`、`TaskResource`

## 評価指標

| 優先 | 指標 | 取得方法 |
|------|------|----------|
| 1 | 修正工数（ファイル数・行数） | `composer experiment:metrics` の `git.*`（**after_fix**） |
| 2 | 更新直後のテスト失敗数 | `phpunit.fail` / `newman.fail`（**after_update**） |
| 3 | 作業時間（分） | [metrics-record-template.md](./experiment/metrics-record-template.md) に手動記録 |

## 実験フェーズ

| フェーズ | 説明 |
|----------|------|
| `baseline` | 更新前・CI 緑 |
| `after_update` | 更新適用直後・テスト未修正 |
| `after_fix` | 修正完了・CI 緑 |

```bash
composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1
composer experiment:metrics -- --phase after_update --diff-ref experiment-baseline-v1
composer experiment:metrics -- --phase after_fix --diff-ref experiment-baseline-v1
```

## ベースライン

```bash
./scripts/check-quality.sh
git tag -a experiment-baseline-v1 -m "Experiment baseline: best architecture"
```

手順: [experiment/BEFORE.md](./experiment/BEFORE.md)

## 結果の公開

```bash
composer experiment:record -- --scenario api-spec-change-status-int --write
./scripts/publish-experiment-results.sh --scenario api-spec-change-status-int
```

結果一覧: [experiment/results/RESULTS.md](./experiment/results/RESULTS.md)

## 関連ドキュメント

| ドキュメント | 内容 |
|--------------|------|
| [README.md](../README.md) | プロジェクト概要 |
| [TESTING.md](./TESTING.md) | テストツール |
| [CI.md](./CI.md) | GitHub Actions |
| [experiment/metrics-record-template.md](./experiment/metrics-record-template.md) | 記録テンプレート |
